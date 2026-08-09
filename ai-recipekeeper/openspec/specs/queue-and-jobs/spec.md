# queue-and-jobs Specification

## Purpose

Provides queue configuration, job infrastructure, worker setup, retry/failure handling, and job status tracking for long-running background tasks such as AI recipe generation.

## Requirements

### Requirement: Queue connection configuration
The system SHALL use Redis as the default queue connection and SHALL provide a sync connection for testing.

#### Scenario: Default queue connection is Redis
- **WHEN** the application dispatches a job without specifying a connection
- **THEN** the job is dispatched to the Redis queue connection

#### Scenario: Test environment uses sync driver
- **WHEN** the application runs in the testing environment
- **THEN** jobs execute synchronously (sync driver) and do not require a queue backend

### Requirement: Redis queue connection configuration
The system SHALL configure the Redis queue connection with host, port, database, and queue name via environment variables.

#### Scenario: Redis connection parameters
- **WHEN** the application connects to the Redis queue
- **THEN** it uses the `REDIS_HOST`, `REDIS_PORT`, and `REDIS_QUEUE_CONNECTION` environment variables for connection parameters
- **AND** the default queue name is `default`

### Requirement: Worker configuration
The system SHALL provide sensible worker defaults for processing queued jobs in production.

#### Scenario: Worker retry attempts
- **WHEN** a queued job fails
- **THEN** the worker retries the job up to 3 times before marking it as failed

#### Scenario: Worker job timeout
- **WHEN** a queued job runs for more than 120 seconds
- **THEN** the worker terminates the job and marks it as failed

#### Scenario: Worker sleep delay
- **WHEN** the worker polls for new jobs and finds none
- **THEN** the worker sleeps for 3 seconds before polling again

#### Scenario: Worker max runtime
- **WHEN** the worker runs for more than 3600 seconds (1 hour)
- **THEN** the worker stops accepting new jobs and finishes processing current jobs

### Requirement: Job retry behavior
The system SHALL support configurable retry behavior on individual job classes.

#### Scenario: Job with custom retry count
- **WHEN** a job class defines a `tries` property
- **THEN** the worker retries the job up to the specified number of times

#### Scenario: Job with retry until timestamp
- **WHEN** a job class implements a `retryUntil` method returning a timestamp
- **THEN** the worker continues retrying the job until that timestamp is reached, regardless of the `tries` property

#### Scenario: Job with backoff delay
- **WHEN** a job class defines a `backoff` property or method
- **THEN** the worker waits the specified number of seconds between retry attempts

### Requirement: Job failure handling
The system SHALL invoke a `failed` method on the job class when all retry attempts are exhausted.

#### Scenario: Job failure callback
- **WHEN** a job exhausts all retry attempts
- **THEN** the system calls the `failed(\Throwable $exception)` method on the job instance
- **AND** the job is recorded in the `failed_jobs` table

#### Scenario: Job failure stores exception
- **WHEN** a job fails permanently
- **THEN** the `failed_jobs` table records the job payload, exception message, connection, queue, and failure timestamp

### Requirement: Job status tracking on generation_ia
The system SHALL track the processing status of AI generation requests through the `generation_ia` table.

#### Scenario: Generation status values
- **WHEN** a generation_ia record is created or updated
- **THEN** its `status` field is one of: `pending`, `processing`, `completed`, `failed`

#### Scenario: Default generation status
- **WHEN** a generation_ia record is created
- **THEN** its `status` defaults to `pending`

#### Scenario: Generation status transitions
- **WHEN** a generation job begins processing
- **THEN** the system updates the status to `processing` and records `started_at`
- **AND** when the job completes successfully, the system updates the status to `completed` and records `completed_at`
- **AND** when the job fails permanently, the system updates the status to `failed` and records `error_message`

#### Scenario: Generation job ID tracking
- **WHEN** a generation job is dispatched to the queue
- **THEN** the system stores the queue job ID in the `job_id` column of the `generation_ia` record

### Requirement: Base job class for AI generation
The system SHALL provide an abstract base job class that concrete AI generation jobs (from KAN-15) will extend.

#### Scenario: Base job contract
- **WHEN** a concrete job extends the base `GenerationIaJob` class
- **THEN** the job has default `tries`, `timeout`, `backoff`, and `queue` values
- **AND** the job implements a `handle()` method that the queue worker calls
- **AND** the job implements a `failed()` method that updates the generation status to `failed`

#### Scenario: Base job queue default
- **WHEN** a job extends the base `GenerationIaJob` class
- **THEN** the default queue for the job is `generations`

### Requirement: Local development workflow
The system SHALL provide a documented workflow for running the queue worker during local development.

#### Scenario: Composer dev script includes worker
- **WHEN** a developer runs `composer dev`
- **THEN** the queue worker starts alongside the web server, log viewer, and asset build
- **AND** the worker processes jobs on the default queue

#### Scenario: Standalone worker command
- **WHEN** a developer runs `php artisan queue:work`
- **THEN** the worker starts processing jobs on the default queue with the configured defaults

### Requirement: Queue environment variables
The system SHALL document all queue-related environment variables in `.env.example`.

#### Scenario: Environment variables present
- **WHEN** a developer sets up the project from `.env.example`
- **THEN** the file contains `QUEUE_CONNECTION`, `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT`, and `REDIS_QUEUE_CONNECTION` variables with appropriate defaults
