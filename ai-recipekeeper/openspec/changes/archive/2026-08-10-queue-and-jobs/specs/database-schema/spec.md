## MODIFIED Requirements

### Requirement: AI generation history table
The system SHALL maintain a generation_ia table to track AI recipe generations, including job processing status.

#### Scenario: Generation logging
- **WHEN** an AI recipe is generated
- **THEN** the system stores user_id, prompt, response, model_used, tokens_used, and timestamps

#### Scenario: Generation status column
- **WHEN** the KAN-16 migration is applied
- **THEN** the `generation_ia` table gains a `status` column with values `pending`, `processing`, `completed`, or `failed`
- **AND** the column defaults to `pending`

#### Scenario: Generation job ID column
- **WHEN** the KAN-16 migration is applied
- **THEN** the `generation_ia` table gains a `job_id` column storing the queue job UUID
- **AND** the column is nullable (populated when the job is dispatched)

#### Scenario: Generation error message column
- **WHEN** the KAN-16 migration is applied
- **THEN** the `generation_ia` table gains an `error_message` text column
- **AND** the column is nullable (populated only when the job fails)

#### Scenario: Generation timestamp columns
- **WHEN** the KAN-16 migration is applied
- **THEN** the `generation_ia` table gains `started_at` and `completed_at` timestamp columns
- **AND** both columns are nullable (populated during job processing)
