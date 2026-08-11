## Context

The recipe detail page (`resources/views/recipes/show.blade.php`) is the only recipe page still extending the legacy Bootstrap layout `layouts/app.blade.php`. The rest of the app already runs on the Tailwind v4 + Material 3 design system defined in `resources/css/app.css` and hosted by `resources/views/layouts/dashboard.blade.php` (desktop top nav, mobile bottom nav, footer, flash messages, Inter/Playfair Display fonts, Material Symbols icons). Motivation is in `proposal.md`.

The controller already supplies everything the redesigned page needs — `RecipeWebController@show` loads the recipe with `user`, `etapes`, `ingredients`, `categories`, `avis.user` and computes `$favorite`, `$userReview`, `$ratingAvg`, `$ratingCount`. Reviews, favorites, edit, and delete are fully functional through existing routes, policies, and validation requests. No backend, route, or data-model change is required.

## Goals / Non-Goals

**Goals:**
- Rewrite `show.blade.php` as a server-rendered Blade page on `layouts/dashboard`, reusing every existing layout component and design token
- Preserve 100% of the page's behavior: dynamic data for any `/recipes/{id}`, favorite toggle, gated edit/delete, published/hidden status, review create/edit/delete (one review per user), 404 on hidden recipes of others
- Keep changes to the view file (plus at most a tiny inline script) and one small controller addition so the diff is reviewable and reversible

**Non-Goals:**
- No changes to `AvisWebController`, `FavoriWebController`, routes, policies, requests, or schema
- No new design tokens, no new layout, no shared components (project has none yet), no UI framework/JS library
- Out of scope: migrating `generations/show.blade.php` (last remaining Bootstrap page) or deleting `layouts/app.blade.php`
- No notifications/settings buttons, no new footer links — nothing the existing layout doesn't already provide

## Decisions

1. **Extend `layouts.dashboard` instead of `layouts.app`.**
   The layout is complete and battle-tested by six pages. Alternatives (extending `layouts.app`, building a third layout, converting the legacy layout to Tailwind) were rejected: they'd duplicate or churn working code.
   Result: top nav, bottom mobile nav, footer, flash banner, fonts, and icons come for free; `@section('title')` stays the mechanism for the document title.

2. **Controller change is minimal and one-time.**
   `show()` already computes `$favorite`, `$userReview`, `$ratingAvg`, `$ratingCount`; the hero only needs the existing `$recipe->image_path` + the project-wide `file_exists(public_path(...))` fallback convention. One discovery during implementation: `layouts/dashboard.blade.php` renders the user avatar cell from `$user` and `$initials` (lines 33–38), which `show()` did not pass. Fix: add the same 5-line `$user`/`$initials` computation every other dashboard action (`index`, `browse`, `create`, `edit`) already uses. No other controller, route, or policy change.

3. **Page anatomy (all server-rendered Blade):**
   - **Hero**: full-width `h-64 md:h-96` `object-cover` image with `md:rounded-xl overflow-hidden`, fallback placeholder
   - **Title block**: centered `max-w-3xl`, `font-display-lg`/`text-[28px] md:text-[32px]` responsive headline; author + date subtitle; published/hidden badge (existing `statut` logic)
   - **Metadata bar**: centered `flex flex-wrap`, icon + value pairs (`schedule`, `oven_gen`, `restaurant`, `trending_up`, `star`), each pair rendered only when its field is non-null; divider lines `hidden md:block`
   - **Action row**: Favorite toggle (switch between the existing `favorites.store` / `favorites.destroy` forms based on `$favorite`), Edit button only `@can('update', $recipe)`, Delete button only `@can('delete', $recipe)`
   - **Content split**: `grid grid-cols-1 md:grid-cols-12 gap-*` — ingredients as a `md:col-span-4` `bg-surface-container-low` card, **sticky on desktop only** (`md:sticky top-24`) so it never fights the mobile bottom nav; steps in `md:col-span-8` with `bg-primary` numbered circles
   - **Chips**: reuse the dashboard's `bg-tertiary/10 text-tertiary-container rounded-full` category chip pattern
   - **Reviews**: preserve the existing behavior exactly — rating summary line, "your review" card with edit/delete, list of other reviews, create form when `$userReview` is null — restyled with the design system's input conventions (`rounded-lg border-outline-variant/50 focus:ring-primary`)

4. **Delete confirmation without Bootstrap JS.**
   The legacy page relies on `data-bs-toggle="modal"`, which is dead in `layouts/dashboard` (no Bootstrap bundle loaded). Decision: a plain `DELETE` form with a native `confirm()` dialog via `onsubmit` — zero dependencies. Alternative (custom styled overlay modal) was rejected: it means hand-rolled JS for a marginal visual gain, and native confirm is consistent with a design system that has no modal component.

5. **Review edit toggle without Bootstrap collapse.**
   Replace the legacy `data-bs-toggle="collapse"` edit form with an optional `hidden` class toggled by a small vanilla-JS listener (the project convention is vanilla JS, per config). Alternative (always-visible inline form) is simpler but clutters the premium look.

6. **Star rating display**: Material Symbols with `font-variation-settings: 'FILL' 1;` for filled stars, exactly as the dashboard featured card already renders ratings.

## Risks / Trade-offs

- **Behavior drift while restyling the review section** (one-review-per-user, edit-instead-of-create, admin delete-all) → Mitigation: keep the controller-computed flags (`$userReview`, `$ratingAvg`, `$ratingCount`) as the single source of truth and mirror the legacy template's conditional structure one-to-one.
- **Broken delete flow** if the modal isn't properly replaced → Mitigation: native `confirm()` + plain DELETE form, verified by hand in the apply phase.
- **Sticky ingredients column overlapping the fixed mobile bottom nav** → Mitigation: sticky only at `md:` breakpoint; bottom nav is `md:hidden`, so they never coexist.
- **Missing image assets on some seeded recipes** → Mitigation: keep the `file_exists` fallback; run `php artisan recipe:sync-images` if needed for local verification.
- **`Str::plural` import** — the legacy template uses it unqualified via Blade's auto-import; keeping the same call pattern prevents a runtime error.
- **Scope creep toward `generations/show`** → Mitigation: explicitly out of scope; the legacy layout is left untouched and functional.

## Migration Plan

Single-file template rewrite; no schema, route, or asset-pipeline changes (Tailwind v4 already sources `resources/views/**`). Deploy/rollback = commit/revert of `show.blade.php`. Verification: render `/recipes/{id}` for a few ids covering: with/without image, with/without reviews, own/published/hidden recipes, owner/admin/non-owner states, desktop and mobile widths.

## Open Questions

None — all ambiguities that would affect specs, approach, or task breakdown were resolved during exploration (see the explore report: reviews exist, images are local files, authorization is policy-based).