## Why

KAN-16 — Queue and Jobs provides the infrastructure for processing long-running background tasks. The future AI recipe generation feature (KAN-15) will call an external OpenAI API, which is inherently slow and unreliable. Without queue infrastructure, AI generation would block the HTTP request, time out, and provide a poor user experience. This change establishes the queue configuration, job base classes, worker setup, retry/failure handling, and job status tracking that KAN-15 will consume — without implementing AI generation itself.

## What Changes

- Switch the default queue connection from `database` to `redis` and configure the Redis queue connection
- Add Redis queue environment variables to `.env.example`
- Create a base `GenerationIaJob` abstract class that KAN-15's concrete job will extend
- Add job status tracking columns (`status`, `job_id`, `error_message`, `started_at`, `completed_at`) to the `generation_ia` table via a new migration
- Update the `GenerationIa` model with status constants, casts, and status helper methods
- Configure worker defaults: `--tries=3`, `--timeout=120`, `--sleep=3`, `--max-time=3600`
- Configure job retry behavior (`retryUntil`, `backoff`) and failure handling (`failed()`) in the base job
- Add a `GenerationIaFactory` for testing
- Add Feature tests for queue configuration, job dispatching, status tracking, retry behavior, and failure handling
- Document the local development workflow for running the worker (already partially in `composer dev`)

## Capabilities

### New Capabilities

- `queue-and-jobs`: Queue configuration (Redis connection), job infrastructure (base job class, retry/backoff/failure handling), worker configuration, job status tracking on `generation_ia`, and local development workflow for running the queue worker.

### Modified Capabilities

- `database-schema`: Add status-tracking columns to the `generation_ia` table (status, job_id, error_message, started_at, completed_at).
- `eloquent-models`: Add status constants, casts, and status helper methods to the `GenerationIa` model.

## Impact

- **Code**: `config/queue.php` (modify default connection), `.env.example` (add Redis queue vars), `app/Jobs/GenerationIaJob.php` (new abstract base job), `app/Models/GenerationIa.php` (add status constants/casts/methods), `database/migrations/xxxx_add_status_to_generation_ia_table.php` (new), `database/factories/GenerationIaFactory.php` (new)
- **Database**: one new migration adding columns to `generation_ia`
- **Config**: Redis as default queue connection, worker timeout/retry defaults
- **Tests**: `tests/Feature/QueueConfigurationTest.php`, `tests/Feature/GenerationIaJobTest.php` (new)
- **Dependencies**: none new (Redis via `phpredis` extension already configured in `config/database.php`)
- **Out of scope**: OpenAI API integration, concrete AI generation job implementation, recipe creation from AI response, any KAN-15 functionality
