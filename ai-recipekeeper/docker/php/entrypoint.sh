#!/usr/bin/env bash
set -euo pipefail

# Ensure an environment file with an application key exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=' .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Install dependencies when the bootstrap volumes are empty
if [ ! -f vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --no-progress
fi

# Normalize permissions for bind-mounted storage
chmod -R ug+rwX storage bootstrap/cache

php artisan storage:link --force >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

exec "$@"