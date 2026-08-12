## Why

The project currently has no containerized development environment: every contributor must manually install PHP, MySQL, Redis, Node, and the Vite toolchain, which makes local setup slow and environment-dependent (e.g., the local machine runs a custom PHP build whose `Pdo\Mysql` class availability differs from stock images). There is also no CI pipeline, so pushes and pull requests are merged without an automated quality gate. Containerizing local development and adding GitHub Actions CI makes the project reproducible on Windows + Docker Desktop + WSL2 and verifies every PR automatically.

## What Changes

- Add a `Dockerfile` for a PHP 8.4 application image (deps available: `pdo_mysql`, `redis`, `sqlite`, `intl`, `mbstring`, `curl`), structured so it can later gain production/multi-stage layers without rework.
- Add `docker-compose.yml` defining: PHP application container (port 8000), MySQL 8.4 (named volume for data), Redis 7 Alpine, a queue worker using the same PHP image (explicit `--queue=generations`), and a Node 22 Vite development container (port 5173).
- Use named volumes for `vendor/` and `node_modules/` to avoid Windows bind-mount performance and integrity problems.
- Add `.dockerignore` to keep build context lean and exclude secrets and local data.
- Add `docker/php/php.ini` and `docker/php/entrypoint.sh` (generate app key if missing, `storage:link`, `migrate --force`, storage permissions).
- Add `.github/workflows/ci.yml`: pushes and pull requests, `ubuntu-latest`, PHP 8.4 with `pdo_mysql`, `sqlite`, `intl`, `mbstring`, `curl`; cached `composer install` from `composer.lock`; Node 22, `npm ci`, `npm run build`; `php artisan key:generate`; `vendor/bin/pint --test`; `composer test`. No MySQL/Redis services and no `OPENROUTER_API_KEY` required in CI.
- Document the Docker workflow in README and augment `.env.example` with Docker-oriented host/credential documentation (service-name hosts, `APP_URL=http://localhost:8000`).
- Application business logic, UI, database schema, and the AI generation implementation are NOT changed.

## Capabilities

### New Capabilities
- `development-environment`: containerized local development (Docker/Compose services, environment wiring, queue worker runtime) and the GitHub Actions CI pipeline (dependency install, asset build, lint gate, test suite).

### Modified Capabilities
- None. Existing capability specs (`queue-and-jobs`, `ai-recipe-generation`, `database-schema`, ...) describe application behavior that is unchanged: queues still target `generations`, tests still run on SQLite in-memory with the sync driver, and the `composer dev` workflow remains available as an alternative to Docker. The Docker environment is new infrastructure, not a behavior change to an existing capability.

## Impact

- **New files**: `Dockerfile`, `docker-compose.yml`, `.dockerignore`, `docker/php/php.ini`, `docker/php/entrypoint.sh`, `.github/workflows/ci.yml`.
- **Modified files**: `.env.example` (documentation only), `README.md` (Docker + CI documentation).
- **No changes** to `app/`, `routes/`, `database/`, `config/`, `resources/`, or `tests/`.
- **Environment contract**: `DB_HOST=mysql`, `REDIS_HOST=redis`, `QUEUE_CONNECTION=redis`, queue `generations`, PHP 8.4 in containers and CI (avoids the `Pdo\Mysql` class availability issue verified during exploration on stock PHP 8.3).
- **Secrets**: `.env`, API keys, and local Docker data are never committed; `.dockerignore` and `.gitignore` enforce this.
- **Out of scope**: production deployment/CD workflow, image registry publishing, and host provisioning — deliberately deferred to a later change.

## Assumptions

- CI runs the test suite without MySQL or Redis because `phpunit.xml` pins SQLite in-memory, sync queue, array cache, and array sessions.
- AI integration makes no real external API calls during CI; `OPENROUTER_API_KEY` stays unset and absent from logs.
- Official `php:8.4` images are safe for this project: all locked packages accept PHP 8.4 (framework and laravel/ai `^8.3`, phpunit `>=8.3`).
- Vite build in CI requires outbound network access (the `laravel-vite-plugin` fonts plugin fetches fonts from bunny.net at build time).
- The worker image is the same PHP image with a different command; no separate base image yet.