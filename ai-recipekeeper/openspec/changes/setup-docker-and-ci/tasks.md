## 1. Docker base image

- [x] 1.1 Create `Dockerfile` from `php:8.4-cli` (Debian) installing extensions `pdo_mysql`, `sqlite3`, `intl`, `mbstring`, the `redis` extension via PECL, and the `curl` binary; document the single-stage seam for the future production multi-stage build
- [x] 1.2 Create `docker/php/php.ini` with dev-sane settings (memory limit, upload limits, session/file settings) and opcache present but disabled by default
- [x] 1.3 Create `docker/php/entrypoint.sh` (LF line endings, executable) that: generates `APP_KEY` when empty, fixes storage/bootstrap-cache permissions, runs `php artisan storage:link`, runs `php artisan migrate --force` (respecting a `RUN_MIGRATIONS` flag), then `exec`s the passed command
- [ ] 1.4 Verify the built image boots `php artisan --version` and `php artisan about` without errors on PHP 8.4

## 2. Docker Compose environment

- [x] 2.1 Create `docker-compose.yml` with services `app` (build from `Dockerfile`, port 8000, `php artisan serve --host=0.0.0.0 --port=8000`), `mysql` (mysql:8.4, named volume, healthcheck), `redis` (redis:7-alpine, healthcheck), `queue-worker` (same image, command `php artisan queue:work redis --queue=generations --tries=3 --timeout=120`), and `vite` (node:22-alpine, port 5173)
- [x] 2.2 Wire environment across services: `DB_HOST=mysql`, `REDIS_HOST=redis`, `QUEUE_CONNECTION=redis`, `REDIS_QUEUE=generations`, `APP_URL=http://localhost:8000`, `APP_ENV=local`, `APP_DEBUG=true`, MySQL credentials aligned between `mysql` and `app`
- [x] 2.3 Add named volumes: `db_data` for MySQL persistence, `vendor` and `node_modules` bootstrap volumes for the app; bind-mount the repository for live code
- [x] 2.4 Add healthcheck-based start ordering (`depends_on: condition: service_healthy`) for mysql and redis
- [x] 2.5 Update `vite.config.js` so the Vite container is reachable: `server.host`, `server.port: 5173`, `server.hmr` host wiring; document the file-watching/polling fallback for non-WSL2 mounts
- [ ] 2.6 Verify `docker compose up --build` on Windows + Docker Desktop + WSL2: app reachable on :8000 with `:8000/up` healthy, Vite on :5173, `@vite` assets served via hot file, MySQL data survives `docker compose down` + `up`

## 3. Queue worker verification in Docker

- [ ] 3.1 Verify the `queue-worker` container only processes the `generations` queue and that a generation request follows `pending → processing → completed` (or `failed` with error message) end-to-end in the containerized environment
- [ ] 3.2 Confirm web requests return immediately (202) while the worker processes generation jobs asynchronously

## 4. Build context hygiene

- [x] 4.1 Create `.dockerignore` excluding `.git`, all `.env*` variants, `vendor`, `node_modules`, logs, `.phpunit.cache`, `openspec/`, `Images/`, `files/`, `public/build`
- [ ] 4.2 Confirm no secrets or local data can enter the Docker build context (verify with `docker build` / `docker compose config` review)

## 5. CI pipeline

- [x] 5.1 Create `.github/workflows/ci.yml` triggering on `push` and `pull_request` across the GitFlow branch set (`main`, `develop`, `feature/*`, `fix/*`, `hotfix/*`, `chore/*`), with `concurrency` cancel-in-progress and `permissions: contents: read`
- [x] 5.2 Configure `shivammathur/setup-php` with PHP 8.4, extensions `pdo_mysql`, `sqlite`, `intl`, `mbstring`, composer caching enabled
- [x] 5.3 Configure `actions/setup-node` with Node 22 and npm cache; run `npm ci` and `npm run build`
- [x] 5.4 Add `php artisan key:generate --ansi`, `vendor/bin/pint --test`, and `composer test` steps (no MySQL/Redis services, no `OPENROUTER_API_KEY`)
- [ ] 5.5 Push a PR on `chore/setup-ci-cd-pipeline` and confirm the pipeline is green and caches are reused on the second run; confirm no secrets appear in logs

## 6. Documentation and environment alignment

- [x] 6.1 Augment `.env.example` with commented Docker-oriented variables (`DB_HOST=mysql`, `REDIS_HOST=redis`, `QUEUE_CONNECTION=redis`, `APP_URL=http://localhost:8000`) so host-run and container-run coexist
- [x] 6.2 Document the Docker workflow in README: prerequisites (Docker Desktop + WSL2), `docker compose up --build`, service URLs, queue worker behavior, and volume-reset commands; note `composer dev` remains available for bare-metal use
- [x] 6.3 Confirm bare-metal workflow still works unchanged with the `.env.example` additions

## 7. Final validation

- [x] 7.1 Run the full local test suite (`composer test`) after all changes to confirm no regressions
- [x] 7.2 Run `vendor/bin/pint` on any touched PHP files
- [x] 7.3 Review final diff: no application logic, schema, or AI generation changes; no secrets committed; no Docker artifacts under `openspec/` planning scope