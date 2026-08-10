## Context

The project is a Laravel 13.8 application with MySQL, Sanctum authentication, and Redis-backed queues. The existing infrastructure includes:

- `GenerationIa` model with status constants and helper methods
- Abstract `GenerationIaJob` base class with `tries=3`, `timeout=120`, `backoff=[10,30,60]`, `queue='generations'`, and `failed()` hook
- `Recette` model with `is_ai_generated` boolean, `visibleTo` scope, and all relationships
- `Etape`, `Ingredient`, `Category` models with pivot tables (`recette_ingredient` with `quantity`/`unit`, `recette_categorie`)
- Existing `StoreRecetteRequest` validates etapes, ingredients, and categories arrays
- `RecetteResource` serializes etapes, ingredients, and categories
- `RecettePolicy` enforces owner-or-admin authorization
- Redis queue configured with `generations` queue name
- `OPENROUTER_API_KEY` present in `.env` (not in `.env.example`)

No OpenRouter service, no concrete generation job, no generation controller, no generation routes exist yet.

## Goals / Non-Goals

**Goals:**
- Expose `POST /api/generate`, `GET /api/generations`, `GET /api/generations/{generation}` API endpoints
- Create a concrete `GenerateRecipeJob` that calls OpenRouter and creates recipes
- Build an `OpenRouterService` wrapping Laravel's HTTP client
- Construct prompts server-side and validate AI JSON responses
- Create recipes transactionally (Recette + Etapes + Ingredients + Categories)
- Enforce owner-or-admin authorization on generation resources
- Use existing queue infrastructure (Redis, retry/backoff, `GenerationIaJob` base class)

**Non-Goals:**
- Frontend/Blade UI for AI generation
- AI image generation, streaming, WebSockets, notifications
- Client-chosen AI models or raw prompt submission
- New database tables or schema changes
- Rate limiting (can be added later)
- Recipe editing after AI generation (user edits via existing CRUD)

## Decisions

### Decision: OpenRouter as AI gateway
**Choice**: Use OpenRouter's API (`https://openrouter.ai/api/v1/chat/completions`) with Laravel's built-in `Http` facade.
**Rationale**: OpenRouter provides a unified gateway to multiple models. The HTTP client is already available in Laravel 13.x. The API follows the OpenAI chat completions format.
**Alternatives**: Use OpenAI directly (rejected: project chose OpenRouter); install `openai-php/client` (rejected: unnecessary dependency).

### Decision: Server-side prompt construction
**Choice**: The service layer constructs the system prompt and user message from structured input. The client never sends raw prompts.
**Rationale**: Server-controlled prompts ensure consistent output, prevent prompt injection, and allow refinement without API changes.
**Alternatives**: Client sends raw prompt (rejected: security risk); client sends partial prompt (rejected: still allows injection).

### Decision: JSON-only AI response format
**Choice**: System prompt instructs OpenRouter to return JSON only. The response is decoded with `json_decode` and validated against expected schema. Use `response_format: { type: "json_object" }` when supported.
**Rationale**: Structured JSON is required for database creation. Validating before DB writes prevents partial data. Failing immediately on invalid JSON avoids wasting retries.
**Alternatives**: Parse free-text (rejected: unreliable); function calling (rejected: inconsistent model support).

### Decision: Ingredient matching by name
**Choice**: Use `Ingredient::firstOrCreate` with case-insensitive name matching.
**Rationale**: Users provide free-text names. Case-insensitive matching handles variations. `firstOrCreate` avoids duplicates while allowing new ingredients.
**Alternatives**: Only existing IDs (rejected: defeats AI purpose); always create new (rejected: duplicates); fuzzy matching (rejected: over-engineering).

### Decision: Categories from request, not AI
**Choice**: Client sends existing category IDs. AI is told which categories were selected for context. AI does not create categories.
**Rationale**: Categories are curated. AI creating arbitrary categories would pollute the space.
**Alternatives**: AI returns category names (rejected: creates arbitrary categories); no categories (rejected: inconsistent with manual recipes).

### Decision: Transactional recipe creation
**Choice**: All writes within a single `DB::transaction`. Rollback on any failure. Mark generation as `failed`.
**Rationale**: Prevents partial recipes. Matches existing `RecetteController::store` pattern.
**Alternatives**: Non-transactional with cleanup (rejected: complex); parent-first (rejected: orphan records).

### Decision: Authorization via GenerationIaPolicy
**Choice**: Create `GenerationIaPolicy` following `RecettePolicy` pattern: `view` = owner or admin, `viewAny` = owner sees own, admin sees all.
**Rationale**: Follows established authorization architecture. Consistent with recipes, reviews, favorites.
**Alternatives**: Inline authorization (rejected: violates policy pattern); skip (rejected: security risk).

### Decision: job_id back-fill
**Choice**: In the controller, after dispatch, store the dispatched job's ID back to `generation_ia.job_id` using `$job->getJob()->getQueue()` or by capturing the dispatch result.
**Rationale**: Useful for debugging and linking. The existing `job_id` column exists for this purpose.
**Alternatives**: Skip (rejected: leaves `job_id` null, reducing observability).

### Decision: OpenRouter configuration
**Choice**: Create `config/openrouter.php` with `api_key`, `base_url`, and `model` keys. API key read from `OPENROUTER_API_KEY` env var.
**Rationale**: Follows Laravel config conventions. Centralizes AI provider settings. Model can be changed without code changes.
**Alternatives**: Hardcode in service (rejected: inflexible); use services.php (rejected: mixing concerns).

## Risks / Trade-offs

- **OpenRouter API latency** -> Mitigation: Job timeout of 120s with 3 retries. Queue ensures HTTP request returns immediately.
- **AI returns inconsistent JSON** -> Mitigation: Strict validation before DB writes. Prompt explicitly specifies format. Use `response_format` parameter when supported.
- **Ingredient name collisions** -> Mitigation: `firstOrCreate` with normalized names. Duplicate prevention via unique constraint on `ingredients.name`.
- **OpenRouter rate limits** -> Mitigation: Job retry with backoff handles transient 429s. Future: per-user rate limiting.
- **API key in .env.example** -> Mitigation: Add placeholder only, never real key. Document that users must obtain their own key.
- **Model changes on OpenRouter** -> Mitigation: Configurable model in `config/openrouter.php`. Prompt is model-agnostic (requests JSON).

## Migration Plan

1. Add `OPENROUTER_API_KEY` placeholder to `.env.example`
2. Create `config/openrouter.php`
3. Create `app/Services/OpenRouterService.php`
4. Create `app/Http/Requests/StoreGenerationRequest.php`
5. Create `app/Http/Resources/GenerationIaResource.php`
6. Create `app/Policies/GenerationIaPolicy.php`
7. Create `app/Jobs/GenerateRecipeJob.php`
8. Create `app/Http/Controllers/GenerationController.php`
9. Add routes to `routes/api.php`
10. Create tests
11. Verify: `php artisan test`, `vendor/bin/pint`

Rollback: Remove new files, revert `.env.example` and `routes/api.php` changes. No database rollback needed.

## Open Questions

None. All design decisions are resolved based on the exploration report and user-provided decisions.
