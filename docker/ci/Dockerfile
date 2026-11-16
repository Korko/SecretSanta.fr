# CI image of the php and e2e jobs: Playwright (node, chromium and its system deps), PHP 8.4 like prod, Composer.
# Built by the ci-image job. On any change here, bump CI_IMAGE in .gitlab-ci.yml, and keep the Playwright tag
# in sync with playwright in package-lock.json.
# Needs the project's container registry (Settings > General > Visibility, project features, permissions).
FROM mcr.microsoft.com/playwright:v1.61.1-noble

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update -qq \
    && apt-get install -yqq --no-install-recommends software-properties-common gpg-agent \
    && add-apt-repository -y ppa:ondrej/php \
    && apt-get install -yqq --no-install-recommends git unzip \
        php8.4-cli php8.4-zip php8.4-intl php8.4-bcmath php8.4-sqlite3 php8.4-mbstring php8.4-xml php8.4-curl \
    && apt-get purge -yqq software-properties-common \
    && apt-get autoremove -yqq \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
