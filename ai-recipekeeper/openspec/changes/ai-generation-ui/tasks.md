## 1. Controller and Routes

- [x] 1.1 Create `app/Http/Controllers/GenerationWebController.php` with `create`, `store`, `show` methods
- [x] 1.2 Add web routes: `GET /generations/create`, `POST /generations`, `GET /generations/{generation}`
- [x] 1.3 Add navigation link in `resources/views/layouts/app.blade.php` navbar

## 2. Generation Form View

- [x] 2.1 Create `resources/views/generations/create.blade.php` with form for ingredients (dynamic rows), preferences, constraints, servings, difficulty
- [x] 2.2 Add "Generate with AI" button to `resources/views/recipes/index.blade.php`

## 3. Status Tracking View

- [x] 3.1 Create `resources/views/generations/show.blade.php` with status display, progress indicator, and auto-refresh
- [x] 3.2 Implement JavaScript polling to check status every 3 seconds
- [x] 3.3 Show generated recipe data when status is completed
- [x] 3.4 Show error message when status is failed

## 4. Verification

- [x] 4.1 Test: clicking "Generate with AI" opens the form
- [x] 4.2 Test: submitting the form creates a generation and redirects to status page
- [x] 4.3 Test: status page shows processing indicator and auto-refreshes
- [x] 4.4 Test: completed generation shows recipe with link to view it
- [x] 4.5 Run `php artisan test` to ensure nothing is broken
