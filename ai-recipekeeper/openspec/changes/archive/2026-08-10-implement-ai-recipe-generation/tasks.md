## 1. Configuration and Environment

- [x] 1.1 Add `OPENROUTER_API_KEY=` placeholder to `.env.example`
- [x] 1.2 Create `config/openrouter.php` with `api_key`, `base_url`, and `model` keys

## 2. OpenRouter Service

- [x] 2.1 Create `app/Services/OpenRouterService.php` with `generateRecipe(array $input): array` method
- [x] 2.2 Implement prompt construction (system prompt + user message from structured input)
- [x] 2.3 Implement OpenRouter API call using Laravel `Http` facade
- [x] 2.4 Implement JSON response parsing and validation
- [x] 2.5 Throw descriptive exceptions on API errors and invalid responses

## 3. Form Request and API Resource

- [x] 3.1 Create `app/Http/Requests/StoreGenerationRequest.php` with validation rules for ingredients, preferences, constraints, categories, servings, difficulty
- [x] 3.2 Create `app/Http/Resources/GenerationIaResource.php` for serializing generation status, recipe data, timestamps, and error message

## 4. Authorization

- [x] 4.1 Create `app/Policies/GenerationIaPolicy.php` with `viewAny`, `view` methods (owner or admin)

## 5. Generation Job

- [x] 5.1 Create `app/Jobs/GenerateRecipeJob.php` extending `GenerationIaJob`
- [x] 5.2 Implement `handle()` method: call OpenRouterService, validate response, create recipe transactionally
- [x] 5.3 Implement ingredient matching/creation with `Ingredient::firstOrCreate`
- [x] 5.4 Implement category attachment from request category IDs
- [x] 5.5 Implement etape creation from AI response
- [x] 5.6 Implement transactional recipe creation (Recette + Etapes + Ingredients + Categories)
- [x] 5.7 Store `model_used` and `tokens_used` on GenerationIa after successful completion

## 6. Controller and Routes

- [x] 6.1 Create `app/Http/Controllers/GenerationController.php` with `store`, `show`, `index` methods
- [x] 6.2 Implement `store`: validate, create GenerationIa (pending), dispatch job, return 202
- [x] 6.3 Implement `show`: authorize, return generation with recipe if completed
- [x] 6.4 Implement `index`: authorize, return user's generations (or all for admin)
- [x] 6.5 Add routes to `routes/api.php` under `auth:sanctum` middleware

## 7. Tests

- [x] 7.1 Create Feature test: unauthenticated generation request returns 401
- [x] 7.2 Create Feature test: valid generation request returns 202 with pending status
- [x] 7.3 Create Feature test: GenerationIa record created with correct attributes
- [x] 7.4 Create Feature test: generation job dispatched to generations queue
- [x] 7.5 Create Feature test: successful job creates recipe with is_ai_generated=true
- [x] 7.6 Create Feature test: recipe has correct etapes, ingredients (with pivot), and categories
- [x] 7.7 Create Feature test: generation status endpoint returns correct data
- [x] 7.8 Create Feature test: completed generation response includes recipe
- [x] 7.9 Create Feature test: non-owner cannot view generation (returns 404)
- [x] 7.10 Create Feature test: admin can view any generation
- [x] 7.11 Create Feature test: user lists only own generations
- [x] 7.12 Create Feature test: invalid generation request returns 422
- [x] 7.13 Create Feature test: OpenRouter failure marks generation as failed
- [x] 7.14 Create Feature test: invalid AI JSON marks generation as failed
- [x] 7.15 Create Feature test: database transaction rollback on recipe creation failure
- [x] 7.16 Create Feature test: job retry behavior on transient failure
- [x] 7.17 Create Unit test: OpenRouterService sends correct headers and payload
- [x] 7.18 Create Unit test: prompt construction produces valid system/user messages
- [x] 7.19 Create Unit test: API key is never exposed in responses or error messages
- [x] 7.20 Use HTTP fakes for all OpenRouter API calls in tests (no real API calls)

## 8. Verification

- [x] 8.1 Run `php artisan test` and fix failures
- [x] 8.2 Run `vendor/bin/pint` to enforce code style
- [x] 8.3 Run `openspec validate` and confirm the change validates
