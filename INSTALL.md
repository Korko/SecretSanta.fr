# Prerequisites
In order to install your instance of SecretSanta, you'll need at least PHP8.1 with extensions: cli, bcmath, zip, bz2, sqlite (or mysql).

You'll also need Composer (https://getcomposer.org/) installed.

# Install the vendors
First you'll need to install all composer vendors. To do so, execute the following command:
```sh
composer install
```

# Docker
If you want to use Docker, this project already installs Laravel Sail in dev environment. See the docker-compose.yml file if needed. Once vendors are installed, you can then call
```sh
sail up -d
```

See https://laravel.com/docs/sail for more informations.

In the following doc, replace all `php artisan` commands by `sail artisan`.

# Without Docker
If you don't want to use Docker, you'll need to install more dependencies for PHP such as: fpm, curl, imap, mbstring, xml, intl, readline, msgpack and igbinary

# Setup environment
You'll need to specify which database you are using, etc, to do so, copy the .env.example file into .env and modify the different parameters
```sh
cp .env.example .env
nano .env
```

# Database
Depending on the bdms you've chosen, you'll need to install the according php extension: mysql, sqlite3 or other.
Then, install the needed tables in the database with the command
```sh
php artisan migrate
```

# Cache
If you've chosen to use a cache server, then don't forget the according php extension: memcached or other.

# NGINX
If you want to use NGINX, here's an example of a vhost file:
```nginx
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    server_name secretsanta.fr www.secretsanta.fr;

    root /var/www/secretsanta.fr/public;

    ssl_certificate /etc/letsencrypt/live/secretsanta.fr/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/secretsanta.fr/privkey.pem;

    access_log /var/www/secretsanta.fr/storage/logs/access.log response_time;
    error_log /var/www/secretsanta.fr/storage/logs/errors.log warn;

    if ($host ~ ^www\.(?<domain>.+)$) {
        return 301 $scheme://$domain$request_uri;
    }

    location / {
      try_files $uri $uri/ /index.php?$query_string;
    }

    # Websockets
    location /app {
        proxy_pass             http://127.0.0.1:6001;
        proxy_set_header Host  $host;
        proxy_read_timeout     60;
        proxy_connect_timeout  60;
        proxy_redirect         off;

        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'Upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

You can also use Laravel own webserver, not advised on production.
```sh
php artisan serve --host=0.0.0.0 --port=80
```

# Crontab
To enable the auto prunning and the parsing of email bounces, you'll have to setup the cron tasks in the crontab:
```cron
* * * * * cd secretsanta.fr && php artisan schedule:run
```

# Websockets
SecretSanta.fr uses websockets to keep the draws interfaces up to date without the need to reload the page. To use them, you'll need to have an additional server running in the background:
```sh
php artisan websocket:serve
```

# Workers (Queues)
If you specified something else than "sync" in the queue environment config, then you'll need to have your workers running.
```sh
php artisan queue:work
```

# Supervisor (websockets and workers)
To be sure both your websockets and workers are running, you can use [Supervisor](http://supervisord.org/index.html). Here's two configuration files for websockets and workers.
```ini
[program:secretsanta-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/secretsanta.fr/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/secretsanta.fr/storage/logs/workers.log
stopwaitsecs=3600
```

```ini
[program:secretsanta-websocket]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/secretsanta.fr/artisan websocket:serve
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/secretsanta.fr/storage/logs/websockets.log
stopwaitsecs=3600
```

Then you can run the following commands to finish the setup
```sh
sudo supervisorctl reread
sudo supervisorctl update 
sudo supervisorctl start secretsanta-worker:*
sudo supervisorctl start secretsanta-websocket:*
```

# NPM
If you want to change things in the javascript, you'll need to recompile the assets via the next command:
```sh
npm run build
```
