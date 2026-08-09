## 1. Queue Configuration

- [x] 1.1 Update `config/queue.php`: change default connection from `env('QUEUE_CONNECTION', 'database')` to `env('QUEUE_CONNECTION', 'redis')`
- [x] 1.2 Update `config/queue.php` redis connection: set `queue` to `env('REDIS_QUEUE', 'generations')`
- [x] 1.3 Update `.env.example`: add `QUEUE_CONNECTION=redis`, `REDIS_QUEUE_CONNECTION=default`, `REDIS_QUEUE=generations`
- [x] 1.4 Verify Redis queue connection is functional: `php artisan tinker` → `Redis::connection('default')->ping()`

## 2. Database Migration

- [x] 2.1 Create migration `add_status_to_generation_ia_table`: add `status` (string, default `pending`), `job_id` (nullable string), `error_message` (nullable text), `started_at` (nullable timestamp), `completed_at` (nullable timestamp); `down()` drops all five columns
- [x] 2.2 Run `php artisan migrate` and verify with `php artisan migrate:status`
- [x] 2.3 Verify reversibility: `php artisan migrate:rollback --step=1` then re-migrate

## 3. GenerationIa Model Update

- [x] 3.1 Add status string constants to `GenerationIa`: `STATUS_PENDING`, `STATUS_PROCESSING`, `STATUS_COMPLETED`, `STATUS_FAILED`
- [x] 3.2 Add `status`, `job_id`, `error_message` to `GenerationIa::$fillable`
- [x] 3.3 Add `started_at` and `completed_at` to `GenerationIa::$casts` as `datetime`
- [x] 3.4 Add boolean helper methods: `isPending()`, `isProcessing()`, `isCompleted()`, `isFailed()`

## 4. Base Job Class

- [x] 4.1 Create `app/Jobs/GenerationIaJob.php` as abstract class with `$queue = 'generations'`, `$tries = 3`, `$timeout = 120`, `$backoff = [10, 30, 60]`
- [x] 4.2 Define abstract `handle()` method
- [x] 4.3 Implement `failed(\Throwable $exception)` method that updates the generation status to `failed` and stores `error_message`
- [x] 4.4 Add constructor accepting `GenerationIa $generation` and storing it as a property

## 5. Factory

- [x] 5.1 Create `database/factories/GenerationIaFactory.php` with default state (`status` = `pending`, `model_used` = `gpt-4`)
- [x] 5.2 Add `processing()`, `completed()`, and `failed()` states to the factory

## 6. Tests

- [x] 6.1 `QueueConfigurationTest`: verify default connection is `redis`, verify `generations` queue name on the redis connection, verify `sync` driver is used when `QUEUE_CONNECTION=sync`
- [x] 6.2 `GenerationIaJobTest`: dispatch a test job extending `GenerationIaJob`, verify it executes (sync), verify status transitions (pending → processing → completed), verify `started_at` and `completed_at` are set
- [x] 6.3 `GenerationIaJobTest`: verify failed job updates status to `failed` and stores `error_message`
- [x] 6.4 `GenerationIaJobTest`: verify base job defaults (`tries`, `timeout`, `backoff`, `queue`)
- [x] 6.5 `GenerationIaModelTest`: verify status constants, helper methods, and casts
- [x] 6.6 Create a concrete test job class in `tests/` for testing the base class behavior

## 7. Verification

- [x] 7.1 Run `php artisan test` and fix failures
- [x] 7.2 Run `vendor/bin/pint` to enforce code style
- [x] 7.3 Run `openspec validate` and confirm the change validates
