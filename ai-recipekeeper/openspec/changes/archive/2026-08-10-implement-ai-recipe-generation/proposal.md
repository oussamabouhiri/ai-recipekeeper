## Why

Users want to generate recipes from ingredients they have on hand, without manually writing each recipe. The project already has the queue infrastructure (KAN-16), the `GenerationIa` model with status tracking, and the `Recette`/`Etape`/`Ingredient`/`Category` models — but no concrete AI generation job, no OpenRouter integration, and no API endpoints to trigger or check generation status. This change connects these pieces into a working asynchronous AI recipe generation feature.

## What Changes

- Add an OpenRouter configuration file (`config/openrouter.php`) with API key, base URL, and default model — sourced from `OPENROUTER_API_KEY` in `.env`
- Add `OPENROUTER_API_KEY` placeholder to `.env.example`
- Create an `OpenRouterService` that calls the OpenRouter API using Laravel's HTTP client
- Create a concrete `GenerateRecipeJob` extending the existing abstract `GenerationIaJob`
- Create a `GenerationController` with `store` (POST /api/generate), `show` (GET /api/generations/{id}), and `index` (GET /api/generations) endpoints
- Create a `StoreGenerationRequest` Form Request for input validation
- Create a `GenerationIaResource` API Resource for serializing generation status and results
- Add routes under `auth:sanctum` middleware
- Add a `GenerationIaPolicy` for owner-or-admin authorization
- Implement prompt construction that sends structured data to OpenRouter and expects structured JSON back
- Implement AI response validation and transactional recipe creation (Recette + Etapes + Ingredients + Categories)
- Add Feature and Unit tests covering the full generation lifecycle

## Capabilities

### New Capabilities

- `ai-recipe-generation`: Asynchronous AI recipe generation via OpenRouter — API endpoints for creating, listing, and checking generation status; prompt construction; AI response parsing and validation; transactional recipe creation from AI output; OpenRouter service integration; authorization for generation resources.

### Modified Capabilities

- `queue-and-jobs`: The concrete `GenerateRecipeJob` extends the existing abstract `GenerationIaJob`. No spec-level requirement changes — the existing spec already declares that concrete jobs will extend the base class (requirement: "Base job class for AI generation"). The concrete job is an implementation detail, not a new requirement.
- `authorization`: A `GenerationIaPolicy` is added following the existing owner-or-admin pattern. No spec-level requirement changes — the existing spec already declares the policy foundation and ownership check pattern. The new policy follows the established convention.

## Impact

- **Code**: New files — `config/openrouter.php`, `app/Services/OpenRouterService.php`, `app/Jobs/GenerateRecipeJob.php`, `app/Http/Controllers/GenerationController.php`, `app/Http/Requests/StoreGenerationRequest.php`, `app/Http/Resources/GenerationIaResource.php`, `app/Policies/GenerationIaPolicy.php`. Modified — `.env.example`, `routes/api.php`
- **Database**: No schema changes. Existing `generation_ia`, `recettes`, `etapes`, `ingredients`, `recette_ingredient`, `recette_categorie` tables are sufficient.
- **Config**: New `config/openrouter.php` for API key, base URL, and default model
- **Dependencies**: None new. Uses Laravel's built-in `Illuminate\Support\Facades\Http` for API calls. No new Composer packages.
- **API**: Three new endpoints: `POST /api/generate`, `GET /api/generations`, `GET /api/generations/{generation}`
- **Tests**: New Feature tests for generation endpoints, authorization, validation, job lifecycle; Unit tests for OpenRouterService and AI response parsing
- **Out of scope**: Frontend/Blade UI for AI generation, AI image generation, streaming responses, WebSockets, notifications, recipe editing beyond what the AI produces, new database tables, client-chosen AI models, raw prompt submission
