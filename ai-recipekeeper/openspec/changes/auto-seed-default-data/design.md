## Context

The Docker entrypoint (`docker/php/entrypoint.sh`) runs `php artisan migrate --force` after key generation and composer install, but never seeds the database. The seeders (`CategorySeeder`, `IngredientSeeder`, `RealRecipeSeeder`) already define 12 categories, ~80 ingredients, and 26 complete French recipes with steps, categories, and ingredients. The `SyncRecipeImages` command (`recipe:sync-images`) already copies images from `Images/imagesRecipe/` to `public/images/recipes/` and sets `recettes.image_path`. All seeders use `firstOrCreate`/`syncWithoutDetaching` — they are idempotent by design. The `visibleTo` scope on `Recette` already shows all `published` recipes to all users, so once seeded, every user sees the demo recipes.

## Goals / Non-Goals

**Goals:**
- Fresh `docker compose up` produces a fully populated app (categories, ingredients, 26 recipes with images)
- Seeding is automatic — no manual step required after first boot
- Seeding is safe to skip when data already exists (idempotent)
- User-created data is never destroyed

**Non-Goals:**
- Changing the seeder data or adding new recipes (already complete in `RealRecipeSeeder`)
- Modifying the `visibleTo` scope or browse page logic (already works correctly)
- Auto-seeding for native (non-Docker) installs — those use `php artisan db:seed --force` manually
- Image resizing, CDN storage, or upload processing

## Decisions

**D1: Guard seeding with an empty-table check instead of a flag file.**
The entrypoint checks `Recette::count() === 0` before running `db:seed` and `recipe:sync-images`. If any recipes exist (user-created or previously seeded), seeding is skipped.
- *Why:* avoids needing a `.seeded` marker file that could get out of sync; the check is simple and directly expresses the intent ("empty app → seed it").
- *Alternatives considered:* a `storage/app/.seeded` flag file — rejected because it can be deleted accidentally, leaving the app re-seeding on next boot.

**D2: Run seeding and image sync as separate artisan commands in the entrypoint.**
The entrypoint calls `php artisan db:seed --force` then `php artisan recipe:sync-images` sequentially, both gated behind the empty-table check.
- *Why:* keeps concerns separate — the seeder handles database rows, the sync command handles filesystem + `image_path`. Both are already idempotent.
- *Alternatives considered:* having the seeder call `Artisan::call('recipe:sync-images')` internally — couples database seeding to filesystem operations; rejected for testability and because the sync command is useful standalone.

**D3: Remove orphan images during the sync step, not as a separate migration.**
The `SyncRecipeImages` command is extended to delete known orphan filenames from `public/images/recipes/` before linking images.
- *Why:* the sync command already owns that directory; adding cleanup there keeps it in one place.
- *Alternatives considered:* a one-time migration — migrations run once per database, but the orphan files are a filesystem concern, not a schema concern.

**D4: Seeding runs only when `RUN_MIGRATIONS=true` (the existing gate).**
The seeding steps are placed inside the existing `if [ "${RUN_MIGRATIONS:-true}" = "true" ]` block, so the queue-worker service (which shares volumes but does not run migrations) never triggers seeding.
- *Why:* matches the existing pattern; the queue-worker must not race with seeding.

## Risks / Trade-offs

- [Entrypoint runs on every container start] → The empty-table check ensures seeding only runs once. If the check fails (e.g. DB connection error), the `set -e` in the script halts before seeding runs — no partial state.
- [Orphan cleanup deletes files that might be in use] → The 4 orphan filenames are known test artifacts with no recipe references; verified by grepping `recettes.image_path`.
- [Seeding takes time on first boot] → 26 recipes + image copy is fast (< 5s on typical hardware); acceptable for a one-time cost.
- [Native (non-Docker) installs don't auto-seed] → Out of scope; documented in README as a manual step (`php artisan db:seed --force && php artisan recipe:sync-images`).

## Migration Plan

1. Extend `docker/php/entrypoint.sh` to add the empty-table check, seeding, and image sync steps.
2. Extend `SyncRecipeImages` to remove orphan files.
3. Test: `docker compose up --build` on a fresh database → verify 26 recipes, 12 categories, images linked.
4. Test: restart container → verify seeding is skipped (recipes already exist).
5. Rollback: revert entrypoint changes — existing data is untouched.
