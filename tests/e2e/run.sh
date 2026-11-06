#!/usr/bin/env bash
# Runs the e2e flow against `php -S` on a fresh sqlite DB, emails go to the log (the flow reads links there)
set -e
cd "$(dirname "$0")/../.."
export DB_CONNECTION=sqlite DB_DATABASE="$PWD/storage/e2e.sqlite" APP_ENV=local APP_DEBUG=false APP_LOCALE=fr APP_FALLBACK_LOCALE=fr DEBUGBAR_ENABLED=false \
    MAIL_MAILER=log LOG_CHANNEL=single QUEUE_CONNECTION=sync CACHE_STORE=array SESSION_DRIVER=array \
    BROADCAST_CONNECTION=log METRICS_BACKEND=null APP_URL=http://127.0.0.1:8765 LOG=storage/logs/laravel.log
: > "$DB_DATABASE"
php artisan migrate --force -q
: > storage/logs/laravel.log
(cd public && exec php -S 127.0.0.1:8765 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php) > storage/logs/e2e-server.log 2>&1 &
trap 'kill $!' EXIT
sleep 2
timeout 300 node tests/e2e/flow.cjs
