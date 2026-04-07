# CLAUDE.md — Developer Context for football-site

## What This Is

A Laravel 12 full-stack web app for CSKA Sofia football fans (cskafantv.com). Bulgarian-language UI. Features: match tracking, user predictions, player ratings, tactic board, OBS streaming overlay, video library, giveaways, and a Filament admin panel.

## Tech Stack

- **Backend:** PHP 8.2+, Laravel 12, Livewire (full-stack components)
- **Admin:** Filament 3.3 at `/admin`
- **Frontend:** Blade templates, Tailwind CSS 4, Vite 6, Konva.js (tactic board canvas)
- **Auth:** Laravel Sanctum + Socialite (Google OAuth; Facebook commented out)
- **Database:** MySQL (prod), SQLite in-memory (tests)
- **Queue/Cache/Session:** All use database driver
- **External APIs:** LiveScore API (standings), YouTube API, Pusher (broadcasting)
- **SEO:** Spatie Sitemap, Open Graph tags, Google Analytics GA-4

## Key Commands

```bash
# Development (starts server, queue, logs, vite concurrently)
composer dev

# Build frontend assets
npm run dev          # dev server with HMR
npm run build        # production build

# Tests
composer test        # clears config cache, then runs phpunit
php artisan test     # direct test run

# Linting
./vendor/bin/pint    # Laravel Pint (PHP CS Fixer)

# Useful artisan commands
php artisan predictions:calculate-points   # Score user predictions for finished matches
php artisan generate:sitemap               # Regenerate public/sitemap.xml
php artisan player:save-monthly-award      # Calculate player of the month
php artisan predictions:preview-points     # Preview scoring (non-destructive)
php artisan predictions:cleanup-duplicates # Remove duplicate predictions
php artisan sync:team-external-ids         # Sync LiveScore external IDs
```

## Project Structure

```
app/
  Console/Commands/     # 6 artisan commands (predictions, sitemap, awards, sync)
  Filament/Resources/   # 10 admin CRUD resources
  Http/Controllers/     # ObsMatchController, SocialiteController, API PlayerController
  Livewire/
    Pages/              # 15 page components (Home, Players, Match\Show, Tactics, etc.)
    Components/         # 9 reusable components (Navbar, Footer, PredictionModal, etc.)
  Models/               # 16 Eloquent models
  Observers/            # FootballMatchObserver (cache invalidation)
  Services/             # LiveScoreService (external API integration)
resources/
  views/layouts/        # app.blade.php (main), obs.blade.php (OBS overlay)
  views/livewire/       # Livewire component views
  js/app.js             # Main JS — includes ~4000-line Konva.js tactic board
  css/app.css           # Tailwind entry point
routes/
  web.php               # All web routes (public + auth + OBS)
  api.php               # GET /api/players (for tactic board)
database/migrations/    # 28+ migrations
```

## Architecture Decisions

- **Livewire-first frontend:** Almost all pages are Livewire components, not traditional controllers+views. Minimal custom JavaScript (exception: tactic board uses Konva.js heavily in `resources/js/app.js`).
- **No REST API beyond tactic board:** The `/api/players` endpoint is the only API route. Everything else is Livewire wire:click / wire:model.
- **OBS overlay system:** Matches have a real-time OBS overlay at `/match/{slug}/obs` with timer control and score management via POST endpoints. No auth on OBS routes.
- **Filament for admin:** All admin CRUD lives in `app/Filament/Resources/`. Admin access is hardcoded to `cskafantv@gmail.com` in `User::canAccessPanel()`.
- **Observer pattern for cache:** `FootballMatchObserver` clears `live_match` cache key when match data changes.
- **Prediction scoring:** 1pt for correct result (win/draw/loss), +2pts for exact score = 3pts max. Calculated via artisan command or triggered by observer when match is marked finished.

## Models & Key Relationships

- `FootballMatch` is the central model: has homeTeam/awayTeam (Team), MatchLineup, PlayerReview, Prediction. Has slug (auto-generated from teams+date), OBS timer fields, 11 embedded video URL fields.
- `Prediction` → `PredictionResult` (one-to-one): stores user's score guess and calculated points.
- `Player` belongs to `Team`, has soft deletes. `is_coach` flag distinguishes coaches.
- `Standing` belongs to `Team` — league table data. Can be synced from LiveScore API via `LiveScoreService`.
- `Video` uses Spatie Sluggable, has category/category_slug for grouping.
- `MonthlyPlayerAward` calculated from `PlayerReview` averages (min 3 reviews).

## Conventions

- Routes use Livewire component classes directly: `Route::get('/players', Players::class)`
- Match URLs use slugs: `/match/{match:slug}`
- Team logos stored in `storage/app/public/`, accessed via `Storage::url()`
- Bulgarian text throughout views — not using Laravel's localization system, just hardcoded Bulgarian strings
- Font Awesome 6 for icons, Google Fonts (Roboto) for typography
- No custom middleware exists

## Environment Variables to Configure

Beyond standard Laravel vars, these are project-specific:
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` — OAuth
- `LIVESCORE_API_KEY`, `LIVESCORE_API_SECRET` — LiveScore standings data
- `YOUTUBE_API_KEY` — YouTube integration
- `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY` — Contact form
- `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET` — Real-time broadcasting
- Mail configured for `mail.cskafantv.com` via SMTP/SSL

## Testing

- PHPUnit with in-memory SQLite (configured in `phpunit.xml`)
- Test directory: `tests/Feature/` and `tests/Unit/`
- Run: `composer test` or `php artisan test`

## Gotchas

- `resources/js/app.js` is massive (~4000+ lines) — it contains the entire tactic board implementation with Konva.js. Not modularized.
- Admin access check is hardcoded to a single email in `User::canAccessPanel()`, not role-based.
- OBS routes have no authentication — anyone with the match slug can control the timer/score.
- The `.env.example` defaults to SQLite, but production uses MySQL. Make sure to configure DB correctly.
- `FootballMatch` has 11 separate `embed_*` fields for different video content types — these are nullable text columns.
- Facebook OAuth routes are commented out in `web.php`.
- LiveScore API uses hardcoded competition IDs (71 = first league, 140 = second league) and a Bulgarian team name translation map in `LiveScoreService`.
