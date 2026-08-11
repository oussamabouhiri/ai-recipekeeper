## Design

### Architecture

This change modifies only the presentation layer (Blade templates, CSS, JS) and one route definition. No controllers, models, or backend logic change.

### CSS Strategy

- Add auth-specific design tokens as Tailwind v4 `@theme` variables in `resources/css/app.css`
- Use Google Fonts CDN for Playfair Display, Inter, and Material Symbols (loaded in guest layout)
- Keep auth styles isolated: only the guest layout loads these fonts/tokens
- The app layout and admin layout remain on Bootstrap — no cross-contamination

### JavaScript Strategy

- Add password toggle logic to `resources/js/app.js` using vanilla JS event delegation
- Target toggle buttons by `data-toggle-password` attribute pointing to the target input ID
- Toggle `type` attribute between `password` and `text`, swap Material Symbol icon between `visibility_off` and `visibility`

### Asset Strategy

- Copy `Images/Auth/image.png` to `public/images/auth/image.png`
- Reference via `{{ asset('images/auth/image.png') }}` in Blade templates
- No external URLs

### Route Strategy

- Replace the root `/` closure with conditional redirect:
  - Guest → `redirect()->route('login')`
  - Auth → `redirect()->route('dashboard')`
- Existing `RegisteredUserController` redirect to `/` continues to work correctly

### Test Strategy

- Update existing test assertions that match on UI text ("Sign in to your account" → new heading text)
- Add new tests: login page renders, register page renders, root guest redirect, root auth redirect
- Do not weaken existing security tests
