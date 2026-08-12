# AI Recipe Keeper - PHP application image (single-stage development build)
# Seam for the future production multi-stage build: the runtime deps,
# source copy, and entrypoint below stay unchanged when a builder stage
# (composer install --no-dev + npm ci + vite build) is prepended.

FROM php:8.4-cli

# System packages: curl CLI (container healthcheck + service readiness)
RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions. mbstring and pcntl (queue worker signal handling) are
# already compiled into the official image.
# pecl runs in a separate layer: docker-php-ext-install leaves the workdir
# in /usr/src/php, where pecl cannot find config.m4.
RUN docker-php-ext-install intl pdo_mysql sqlite3

RUN cd /tmp \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

COPY . .

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]