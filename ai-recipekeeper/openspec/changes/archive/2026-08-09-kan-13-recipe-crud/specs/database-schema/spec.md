## MODIFIED Requirements

### Requirement: Recipes table
The system SHALL maintain a recipes table to store recipe information.

#### Scenario: Recipe storage
- **WHEN** a recipe is created
- **THEN** the system stores title, description, prep_time, cook_time, servings, difficulty, image_path, statut, user_id, is_ai_generated, and timestamps

#### Scenario: Recipe status
- **WHEN** the KAN-13 migration is applied
- **THEN** the `statut` column is added as an enum with values `published` and `hidden`
- **AND** the column defaults to `published`

#### Scenario: Image field as plain data
- **WHEN** a recipe is created or updated
- **THEN** the `image_path` field is handled as ordinary recipe data
- **AND** KAN-13 does not introduce image upload or storage functionality

#### Scenario: `instructions` column discrepancy
- **WHEN** the KAN-13 database changes are assessed against the MLD
- **THEN** the existing `instructions` column on `recettes` is documented as a known implementation/schema discrepancy: it exists in the current migration and model but is NOT defined in the MLD
- **AND** KAN-13 does not use `instructions` in the API contract, and does not create, remove, or rename the column
- **AND** `etapes` is the authoritative structured source of recipe steps
