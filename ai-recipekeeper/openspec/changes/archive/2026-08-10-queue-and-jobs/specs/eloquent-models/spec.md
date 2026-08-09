## MODIFIED Requirements

### Requirement: AI generation model

The system SHALL provide a GenerationIa model for AI generation entity management, including status tracking and helper methods.

#### Scenario: Generation relationships

- **WHEN** a GenerationIa is loaded
- **THEN** the model defines a belongs-to relationship with user

#### Scenario: Status constants

- **WHEN** the GenerationIa model is used
- **THEN** the model defines string constants for each status value: `STATUS_PENDING`, `STATUS_PROCESSING`, `STATUS_COMPLETED`, `STATUS_FAILED`

#### Scenario: Status casts

- **WHEN** a GenerationIa record is loaded
- **THEN** the `started_at` and `completed_at` fields are cast to `datetime` or `nullable_datetime`
- **AND** the `status` field is accessible as a string

#### Scenario: Status helper methods

- **WHEN** a GenerationIa instance is inspected
- **THEN** the model provides `isPending()`, `isProcessing()`, `isCompleted()`, and `isFailed()` boolean helper methods
