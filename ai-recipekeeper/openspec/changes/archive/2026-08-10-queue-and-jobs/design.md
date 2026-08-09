## Context

See proposal.md - Why for motivation. The project is a Laravel 13.8 application with:
- `config/queue.php` already defines a `redis` connection (driver `redis`, connection `default`, queue `default`, retry_after 90)
- The default queue connection is currently `database`
- `config/database.php` already configures Redis with `phpredis` client, default connection (DB 0), and cache connection (DB 1)
- The `jobs`, `job_batches`, and `failed_jobs` tables already exist (migration `0001_01_01_000002_create_jobs_table.php`)
- The `generation_ia` table exists with columns: `id`, `user_id`, `prompt`, `response`, `model_used`, `tokens_used`, timestamps
- The `GenerationIa` model exists with fillable fields, `tokens_used` cast, and `user()` BelongsTo relationship
- The `User` model has a `generations()` HasMany relationship
- No `app/Jobs/` directory exists
- No queue-related tests exist
- PHPUnit config uses `QUEUE_CONNECTION=sync` for tests
- The `composer dev` script already runs `php artisan queue:listen --tries=1 --timeout=0` concurrently

Constraints from the proposal: Redis as default queue, no AI implementation (KAN-15), no database schema changes beyond status columns on `generation_ia`, no new packages.

## Goals / Non-Goals

**Goals:**
- Switch default queue connection from `database` to `redis` with proper Redis queue configuration
- Add environment variables for Redis queue configuration to `.env.example`
- Create an abstract `GenerationIaJob` base class with sensible defaults (`tries`, `timeout`, `backoff`, `queue`) and status-tracking lifecycle hooks
- Add status-tracking columns to `generation_ia` (`status`, `job_id`, `error_message`, `started_at`, `completed_at`) via a reversible migration
- Update `GenerationIa` model with status constants, datetime casts, and boolean helper methods
- Configure worker defaults (tries=3, timeout=120, sleep=3, max-time=3600) via documentation and `.env.example`
- Add a `GenerationIaFactory` for test data generation
- Add Feature tests verifying queue configuration, job dispatch, status tracking, retry behavior, and failure handling
- Document the local development workflow (already partially in `composer dev`)

**Non-Goals:**
- Implementing the concrete AI generation job (KAN-15)
- OpenAI API integration or any AI SDK packages
- Recipe creation from AI responses
- Job batching or chain/pipe patterns
- Dashboard or monitoring UI for queue health (Horizon)
- Queue connection failover or routing logic
- Priority queues or delayed dispatch
- Image processing or file handling jobs

## Decisions

### Decision: Redis as default queue connection
**Choice**: Change `QUEUE_CONNECTION` default from `database` to `redis` in `config/queue.php`; add a dedicated `generations` queue name on the Redis connection.
**Rationale**: Redis provides better performance than database for queue operations, is the Laravel-recommended backend for production queues, and is already configured in `config/database.php`. The `redis` queue connection in `config/queue.php` is already defined — only the default and queue name need adjustment.
**Alternatives**: Keep `database` driver (rejected: slower polling, lock contention, not suitable for high-throughput AI generation); use `sqs` or `beanstalkd` (rejected: adds infrastructure complexity not warranted for this project).

### Decision: Dedicated `generations` queue name
**Choice**: Set the Redis queue name to `generations` via `REDIS_QUEUE=generations` in `.env.example`, and set `$queue = 'generations'` on the base job class.
**Rationale**: Isolating AI generation jobs on their own queue allows the worker to prioritize them independently and makes it trivial to scale workers per queue. It also prevents generation jobs from competing with other future queue work.
**Alternatives**: Use `default` queue (rejected: mixes concerns, harder to isolate and monitor); multiple queues per job type (rejected: over-engineering for current scope).

### Decision: Abstract base job class (`GenerationIaJob`)
**Choice**: Create `app/Jobs/GenerationIaJob.php` as an abstract class with default `tries = 3`, `timeout = 120`, `backoff = [10, 30, 60]`, `queue = 'generations'`, and abstract `handle()` method. The `failed()` method receives the generation record and updates status to `failed` with `error_message`.
**Rationale**: KAN-15 will create a concrete job extending this class. The base class encapsulates the retry/timeout/failure lifecycle so the concrete job only implements the AI call logic. Using an abstract class (not a trait) gives a clear inheritance contract and keeps the job hierarchy simple.
**Alternatives**: Trait-based approach (rejected: less discoverable, no type hint for the concrete job); interface only (rejected: no default behavior reuse); no base class (rejected: KAN-15 would duplicate retry/timeout/status logic).

### Decision: Status tracking via new columns on `generation_ia`
**Choice**: Add `status` (string, default `pending`), `job_id` (nullable string), `error_message` (nullable text), `started_at` (nullable timestamp), `completed_at` (nullable timestamp) to the existing `generation_ia` table.
**Rationale**: The `generation_ia` table is the natural home for status — it already stores the prompt, response, model, and tokens. Adding columns avoids a separate `job_status` table and keeps generation lifecycle data co-located. The status column uses a string (not enum) for SQLite compatibility and simplicity.
**Alternatives**: Separate `job_status` table (rejected: over-normalized for a single-entity lifecycle); status on `failed_jobs` only (rejected: doesn't track `pending`/`processing`/`completed`); PHP enum cast (deferred — string + constants is sufficient, enum cast can be layered later).

### Decision: Status constants on the model, not a PHP enum
**Choice**: Define `STATUS_PENDING`, `STATUS_PROCESSING`, `STATUS_COMPLETED`, `STATUS_FAILED` as string constants on `GenerationIa`, with boolean helper methods (`isPending()`, etc.).
**Rationale**: Matches the project's existing pattern (string-based status, no PHP enums used elsewhere). Constants provide IDE autocomplete and type safety without requiring a `BackedEnum` cast. The boolean helpers keep controller/job code readable.
**Alternatives**: PHP `BackedEnum` cast (deferred — adds surface for no requirement yet, can be layered on later without schema change); inline string comparisons (rejected: error-prone, not self-documenting).

### Decision: Worker defaults via env vars and documentation
**Choice**: Document worker defaults (`--tries=3 --timeout=120 --sleep=3 --max-time=3600`) in `.env.example` comments and in the design/tasks. Do not create a custom artisan command or config file for worker options.
**Rationale**: Laravel's `queue:work` accepts these as command-line flags. The `composer dev` script already runs the worker. Adding env vars for worker options is unnecessary complexity — developers can pass flags directly or use a Procfile in production.
**Alternatives**: Custom `QueueWorkerCommand` (rejected: over-engineering); `config/queue.php` worker section (rejected: non-standard Laravel pattern); supervisor config (deferred to deployment, not KAN-16 scope).

### Decision: Feature tests using sync driver + Redis connection test
**Choice**: Use the existing `QUEUE_CONNECTION=sync` in `phpunit.xml` for most tests (jobs execute immediately). Add one dedicated test that verifies the Redis queue connection is configured and available (skipped if Redis is not running).
**Rationale**: Sync driver makes tests fast and deterministic. The Redis connection test validates configuration without requiring Redis in CI. The base job class and status tracking are testable with sync since the lifecycle hooks (`handle`, `failed`) are called synchronously.
**Alternatives**: Test against real Redis (rejected: requires Redis server in CI, flaky); mock Redis (rejected: doesn't validate configuration).

## Risks / Trade-offs

- **Redis required in production** → Mitigation: Redis is already configured in `config/database.php`; the `.env.example` documents the connection. If Redis is unavailable, falling back to `database` driver is a one-line env change.
- **Status column uses string, not enum** → Mitigation: validated at application layer via constants; SQLite-compatible; can be migrated to PHP enum later without schema change.
- **`job_id` is nullable** → Mitigation: populated when the job is dispatched; allows generation_ia records created outside the queue (future manual triggers) to exist without a job ID.
- **Worker timeout of 120s** → Mitigation: AI generation via OpenAI typically completes in 10-60s; 120s provides headroom. Can be overridden per-job via the `$timeout` property.
- **`composer dev` already runs queue:listen** → Mitigation: the script uses `--tries=1 --timeout=0` (dev defaults); production worker configuration is separate and documented.

## Migration Plan

1. New migration `add_status_to_generation_ia_table`: adds `status` (string, default `pending`), `job_id` (nullable string), `error_message` (nullable text), `started_at` (nullable timestamp), `completed_at` (nullable timestamp); `down()` drops all five columns.
2. Update `config/queue.php`: change `'default'` from `env('QUEUE_CONNECTION', 'database')` to `env('QUEUE_CONNECTION', 'redis')`.
3. Update `.env.example`: add `REDIS_QUEUE_CONNECTION=default`, `REDIS_QUEUE=generations`, add comments for worker options.
4. Create `app/Jobs/GenerationIaJob.php` (abstract base class).
5. Update `app/Models/GenerationIa.php` (status constants, casts, helper methods).
6. Create `database/factories/GenerationIaFactory.php`.
7. Create Feature tests.
8. Implementation order: migration → config/env → model update → base job class → factory → tests → verification.
9. Verification: `php artisan test`, `vendor/bin/pint`, `openspec validate`.

## Open Questions

- **Worker process management in production**: supervisor/Procfile configuration is deployment-specific and out of KAN-16 scope. The base job class and queue config are driver-agnostic.
- **Redis DB index for queue**: defaults to DB 0 (same as `default` Redis connection). If cache collisions arise, a separate DB can be configured via `REDIS_QUEUE_DB` — not needed now.
