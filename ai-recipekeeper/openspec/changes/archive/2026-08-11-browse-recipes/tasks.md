## 1. Route and Controller

- [x] 1.1 Add `GET /browse` route to `routes/web.php` inside the `auth` middleware group, before the existing `Route::resource('recipes', ...)` line. Name it `recipes.browse`. Point to `RecipeWebController@browse`. The route should be accessible to both authenticated users and guests (no `auth` middleware restriction on this specific route), so place it outside the `auth` middleware group or create a separate group with `auth` optional.

- [x] 1.2 Add the `browse` method to `app/Http/Controllers/RecipeWebController.php`. The method accepts `Request $request` and returns `View`. Build the query: `Recette::query()->visibleTo($request->user())->with(['categories', 'favoris'])`. Apply search filter when `$request->input('search')` is present: `->where(fn($q) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"))`. Apply category filter when `$request->input('category')` is present: `->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId))`. Order by `->latest()`, paginate with `->paginate(12)->withQueryString()`. Load all categories: `Category::orderBy('name')->get()`. Compute favorite IDs for current user: `$request->user() ? $request->user()->favoris()->pluck('recette_id') : collect()`. Pass `$recipes`, `$categories`, `$favoriteIds`, and `$search` (from request) to the view `recipes.browse`.

## 2. Browse Blade View

- [x] 2.1 Create `resources/views/recipes/browse.blade.php` extending `layouts.dashboard`. Set the page title section to "Browse Recipes". Create the header section with heading "Discover Your Next Meal", supporting description text, and a "Generate with AI" button linking to `route('generations.create')`. Use the same Tailwind/MD3 patterns as the dashboard's welcome hero section.

- [x] 2.2 Add the search form section below the header. Use a `<form>` with `method="GET"` action pointing to the current route (`route('recipes.browse')`). Include a text input with name `search`, pre-filled with `$search` using `old('search', $search)`. Add a Material Symbols `search` icon as a prefix inside the input container. Style the input using the same Tailwind patterns as the dashboard's form elements (border, rounded-lg, focus ring, font-body-md).

- [x] 2.3 Add the category chip section below the search form. Render a horizontal scrollable row of chips. The first chip is "All" linking to `route('recipes.browse')` (or `route('recipes.browse', ['search' => $search])` if search is active). For each category in `$categories`, render a chip linking to `route('recipes.browse', ['category' => $category->id, 'search' => $search])`. Highlight the active chip using the request's `category` parameter: compare `$category->id == request('category')` to apply primary background vs outline style. Use `flex overflow-x-auto gap-2 pb-2` for horizontal scrolling on mobile.

- [x] 2.4 Add the recipe grid section. Use `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6`. For each `$recipe` in `$recipes`, render a card as an `<a>` linking to `route('recipes.show', $recipe)`. The card contains: an image area with aspect-[3/2] and `bg-surface-container-high`, using the existing image fallback pattern (check `$recipe->image_path && file_exists(public_path(...))`, show `<img>` or restaurant icon placeholder); a difficulty badge overlaid on the image (top-left, using the same pattern as the dashboard featured card); a favorite button overlaid on the image (top-right, form POST/DELETE to existing favorites routes, only shown for authenticated users via `@auth`); a content section with title (line-clamp-2), description (line-clamp-2); and a metadata footer with prep time, cook time, and servings using Material Icons. Style the card using `bg-surface-container-lowest rounded-xl border border-outline-variant/30 overflow-hidden shadow-sm hover:shadow-md transition-shadow group`. Use flex-col with `flex-grow` on the content section to keep cards equal height.

- [x] 2.5 Add the empty state section, shown when `$recipes->isEmpty()`. Use the same pattern as the dashboard's empty favorites state: `bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant/50 p-10 text-center flex flex-col items-center gap-4`. Include a restaurant icon, a message ("No recipes found" when search/filter is active, "No recipes yet" when collection is empty), and a button to clear filters or create a recipe.

- [x] 2.6 Add pagination below the grid using `{{ $recipes->links() }}`. Style it to match the Tailwind/MD3 theme. Ensure the pagination links preserve query parameters (search, category) via `withQueryString()` already applied in the controller.

## 3. Favorite Toggle on Cards

- [x] 3.1 In the browse view recipe card, add the favorite button for authenticated users. Wrap in `@auth` directive. If `$favoriteIds->contains($recipe->id)`, render a DELETE form to `route('favorites.destroy', $recipe->id)` with `@csrf @method('DELETE')` and a filled heart icon. Otherwise, render a POST form to `route('favorites.store')` with `@csrf` and a hidden input `name="recette_id" value="{{ $recipe->id }}"` and an outline heart icon. Style the button as an absolute-positioned circle on the top-right of the image area with `bg-surface-container-lowest/90 backdrop-blur-sm`. After submission, the redirect should preserve current query params by reconstructing the URL from `request()->query()`.

## 4. Navigation Updates

- [x] 4.1 Update `resources/views/layouts/dashboard.blade.php` desktop top nav (line 20-25): Add a "Browse" link pointing to `route('recipes.browse')` between the "Dashboard" and "Recipes" links. Style it consistently with existing nav items. Consider making the "Recipes" link point to `route('recipes.index')` (My Recipes) or rename it to "My Recipes" for clarity.

- [x] 4.2 Update `resources/views/layouts/dashboard.blade.php` mobile bottom nav (line 68-87): Add a "Browse" item pointing to `route('recipes.browse')` with a `explore` or `menu_book` Material icon. Place it as the second item (after Home). Adjust the existing "Recipes" item to link to `route('recipes.index')` and rename to "My Recipes" or keep as "Recipes" pointing to the management page.

- [x] 4.3 Update `resources/views/layouts/dashboard.blade.php` footer nav (line 56-61): Add a "Browse" link pointing to `route('recipes.browse')` alongside the existing navigation links.

## 5. Tests

- [x] 5.1 Create `tests/Feature/BrowseRecipesTest.php` with `use RefreshDatabase;` trait. Write tests for: authenticated user can access `/browse` (assertOk); guest can access `/browse` (assertOk); published recipes appear on browse page (assertSee recipe title); hidden recipes do not appear for guests (assertDontSee hidden recipe title); owner sees own hidden recipes on browse.

- [x] 5.2 Add search tests to `BrowseRecipesTest`: search by exact title returns matching recipe; partial title match returns recipe; description match returns recipe; empty search returns all recipes; search with no results shows empty state; search term is preserved in pagination links.

- [x] 5.3 Add category filter tests: filter by category returns only recipes in that category; "All" (no category param) returns all recipes; category filter combined with search works correctly; category parameter persists through pagination.

- [x] 5.4 Add favorite display tests: authenticated user sees favorite button on cards; favorited recipe shows correct state (filled heart); guest does not see favorite button; favorite toggle (POST/DELETE) works and redirects back to browse with preserved filters.

- [x] 5.5 Add pagination test: with 13+ recipes, browse page shows pagination links; default page size is 12.

## 6. Verification

- [x] 6.1 Run `php artisan test` to verify all existing tests still pass (no regressions).

- [x] 6.2 Run `php artisan route:list` to verify the new `/browse` route is registered correctly.

- [x] 6.3 Manually verify in browser (or via test) that the browse page renders with the dashboard layout, shows recipe cards, search works, category filtering works, favorite toggle works, and pagination preserves filters.
