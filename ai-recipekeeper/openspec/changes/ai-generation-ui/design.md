## Context

Backend API exists: `POST /api/generate` (returns 202 with generation ID), `GET /api/generations/{id}` (returns status + recipe when complete). Uses Laravel queue with `GenerateRecipeJob`. Models: `GenerationIa` with status tracking (pending → processing → completed/failed).

## Goals / Non-Goals

**Goals:**
- Simple form to submit AI generation request (ingredients + preferences)
- Status page with auto-refresh showing progress
- Display generated recipe when complete
- Add "Generate with AI" button to recipes index

**Non-Goals:**
- Real-time WebSocket updates (use polling instead)
- Edit/regenerate functionality
- Mobile-specific UI (responsive Bootstrap is sufficient)

## Decisions

1. **Use existing API endpoints** — Web controller calls the same API logic via `OpenRouterService` directly, not HTTP calls to itself.

2. **Polling for status** — Use JavaScript `setInterval` to poll status every 3 seconds until completed/failed. Simple and reliable.

3. **Redirect to recipe on success** — When generation completes, redirect to the recipe show page.

4. **Bootstrap 5 consistent** — Match existing UI patterns (cards, tables, forms, badges).

## Risks / Trade-offs

- **Polling overhead** — Minor; only active on status page. Mitigation: Stop polling when complete.
- **No WebSocket** — User won't see instant updates. Mitigation: 3-second polling is fast enough for this use case.
