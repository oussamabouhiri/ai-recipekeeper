## Context

See proposal.md — Why. Relevant current state: `RealRecipeSeeder` already holds 25 complete French recipes (ingredients with quantity/unit, steps, categories by name); `SyncRecipeImages` (`recipe:sync-images`) already copies `Images/imagesRecipe/*` into `public/images/recipes/<slug>.<ext>` and sets `recettes.image_path`; the entrypoint (`docker/php/entrypoint.sh`) runs `migrate` behind the `RUN_MIGRATIONS` gate; the DB connection is MySQL in docker (`DB_HOST=mysql`). All seeding uses `firstOrCreate`/`syncWithoutDetaching` → idempotent by design.

## Goals / Non-Goals

**Goals:**
- Fully populated app (categories, ingredients, 26 recipes, images) via a simple, repeatable seeding flow on this branch's native stack (Laragon PHP + MySQL)
- Single source of truth for recipe data (the seeder) and image linking (the existing sync command)
- Repeatable: re-running the flow on seeded data changes nothing

**Non-Goals:**
- Image upload/processing, resizing, or CDN storage — out of scope, existing upload flow untouched
- Auto-seed on boot — this branch has no docker entrypoint; the flow is an explicit manual step (a follow-up change can add auto-seed when the docker stack lands on this branch)
- Rebuilding the seeders from scratch — existing seeders are kept and extended

## Decisions

**D1: Seeding is an explicit two-command flow, documented in the README.**
`php artisan db:seed --force` then `php artisan recipe:sync-images`, executed against the local MySQL (`127.0.0.1`, DB `ai_recipekeeper`).
- *Why:* this branch runs natively (no docker/entrypoint); both commands are idempotent (`firstOrCreate`, `syncWithoutDetaching`, conditional file copy), so the flow is safe to re-run and never duplicates data.
- *Alternatives considered:* entrypoint auto-seed on empty DB (rejected — the docker stack is not on this branch); seeder invoking `Artisan::call('recipe:sync-images')` (couples seeding to filesystem copying, still needs a manual trigger; rejected for testability).

**D2: 26th recipe lives in `RealRecipeSeeder` like its siblings; image renamed to match the title slug.**
The sync command keys on `Str::slug(recipe title)` == `Str::slug(file name)`. « Saumon Glacé au Miel et Carottes Rôties » → `saumon-glace-au-miel-et-carottes-roties.png`, so the typo'd `honey-glazed-salamon-…` file is renamed and the English name dropped, matching the 25 other French titles.
- *Why:* zero changes to the sync command; consistency of the dataset.
- *Alternatives considered:* keep English title + fix typo (`honey-glazed-salmon-…`) — works identically, rejected for dataset coherence (app UI is French).

**D3: Recipe data quality is treated as seed data, not code.**
Ingredients reference the exact names already present in `IngredientSeeder` so `firstOrCreate` reuses them (e.g. « Saumon », « Carottes », « Miel », « Sauce soja »); categories come from the 12 seeded ones. Steps/quantities are authored as plain arrays, same shape as the existing 25 recipes.
- *Why:* no schema or model changes needed (`recettes.image_path` exists, model fillable already includes it).

**D4: Orphan cleanup is a one-time file removal, not a runtime rule.**
The 4 test images in `public/images/recipes/` are deleted once; no spec or command enforces "no orphans" at runtime.
- *Why:* `public/` is a dev bind-mount; runtime enforcement would be over-engineering for demo data.

## Risks / Trade-offs

- [Seed data drift: 26 images vs. seeder count] → The sync command warns nothing when a source has no match; the acceptance check in tasks asserts 26 recipes and 26 synced images to catch drift early.
- [Renamed image leaves a stale `image_path`] → Verified: no recipe row or seeder references the old `honey-glazed-…` slug; `recipe:sync-images --clear` exists if a stale path ever appears.
- [Seeding is manual — fresh clones forget to run it] → Mitigated by documenting the two commands in the README.
- [`.dockerignore`-style exclusions are moot on this branch] → The source images live in `Images/imagesRecipe/` and the sync copies into `public/images/recipes/`, both present in this working tree; nothing to configure.

## Migration Plan

1. Rename the source image, extend `RealRecipeSeeder` (new recipe method, registered in `getRecipes()`).
2. Document the seeding flow in the README.
3. On an existing local database: `php artisan db:seed --force` && `php artisan recipe:sync-images` (idempotent; safe to run on live data).
4. Rollback: remove the new recipe method and the README section — existing data is untouched by rollback.

## Open Questions

- Whether the 26 images should later be committed into the repo via Git LFS or a CDN for production builds — deferrable, no impact on this change's approach.
