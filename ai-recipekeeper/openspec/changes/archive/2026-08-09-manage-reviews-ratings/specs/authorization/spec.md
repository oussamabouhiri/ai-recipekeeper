## ADDED Requirements

### Requirement: Review resource authorization
The system SHALL provide a Policy for the review resource so users only update or delete reviews they are authorized to modify, and admins can moderate any review.

#### Scenario: Owner updates or deletes own review
- **WHEN** the owner of a review requests its update or deletion
- **THEN** the Policy allows the action

#### Scenario: Admin moderates any review
- **WHEN** an admin requests the update or deletion of any review
- **THEN** the Policy allows the action

#### Scenario: Non-owner modification denied
- **WHEN** a user who is neither the review owner nor an admin requests its update or deletion
- **THEN** the Policy denies the action and the system rejects the request with a 403 response
