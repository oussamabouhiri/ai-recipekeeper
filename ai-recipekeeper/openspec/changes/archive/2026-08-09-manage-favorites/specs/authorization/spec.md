## ADDED Requirements

### Requirement: Favorite resource authorization
The system SHALL provide a Policy for the favorite resource so users only remove favorites they are authorized to remove, and admins can moderate any favorite.

#### Scenario: Owner removes own favorite
- **WHEN** the owner of a favorite requests its removal
- **THEN** the Policy allows the action

#### Scenario: Admin moderates any favorite
- **WHEN** an admin requests the removal of any favorite
- **THEN** the Policy allows the action

#### Scenario: Non-owner removal denied
- **WHEN** a user who is neither the favorite owner nor an admin requests its removal
- **THEN** the Policy denies the action and the system rejects the request with a 403 response
