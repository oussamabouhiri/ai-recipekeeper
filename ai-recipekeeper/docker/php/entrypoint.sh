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

# Install dependencies when the bootstrap volumes are empty.
# Only the app service bootstraps (RUN_MIGRATIONS=true): the queue-worker
# shares the vendor volume and must not race composer install.
if [ ! -f vendor/autoload.php ] && [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --no-progress
fi

# Normalize permissions for bind-mounted storage
chmod -R ug+rwX storage bootstrap/cache

php artisan storage:link --force >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force

    # Seed demo data on a fresh database (no recipes yet)
    RECIPE_COUNT=$(php artisan tinker --execute="echo \App\Models\Recette::count();" 2>/dev/null || echo "0")
    if [ "$RECIPE_COUNT" = "0" ]; then
        echo "Empty database detected — seeding demo data..."
        php artisan db:seed --force
        php artisan recipe:sync-images
        echo "Demo data seeded successfully."
    else
        echo "Database already contains recipes — skipping seed."
    fi
fi

exec "$@"