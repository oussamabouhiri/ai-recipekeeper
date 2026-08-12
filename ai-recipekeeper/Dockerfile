# AI Recipe Keeper - PHP application image (single-stage development build)
# Seam for the future production multi-stage build: the runtime deps,
# source copy, and entrypoint below stay unchanged when a builder stage
# (composer install --no-dev + npm ci + vite build) is prepended.

FROM php:8.4-cli

# System packages: curl CLI (container healthcheck + service readiness)
RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        libicu-dev \
        libsqlite3-dev \
        unzip \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions. mbstring and pcntl (queue worker signal handling) are
# already compiled into the official image.
# NOTE: pdo_sqlite is used instead of sqlite3: the PHP 8.4 source tarball
# ships ext/sqlite3/config0.m4 (not config.m4), so phpize fails.
# pecl runs in a separate layer: docker-php-ext-install leaves the workdir
# in /usr/src/php, where pecl cannot find config.m4.
RUN docker-php-ext-install intl pdo_mysql pdo_sqlite

RUN cd /tmp \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

COPY . .

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint"]
# --no-reload: without it, Laravel 13's serve command strips every env var
# not on its passthrough whitelist from the php -S child process, so the
# server falls back to .env values (e.g. DB_HOST=127.0.0.1) instead of the
# container environment (DB_HOST=mysql).
CMD ["php", "artisan", "serve", "--no-reload", "--host=0.0.0.0", "--port=8000"]