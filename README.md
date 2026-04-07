# CSKA Fan TV — Football Fan Platform

A full-stack web application for CSKA Sofia football fans. Features match tracking, score predictions, player ratings, an interactive tactic board, OBS streaming overlays, video library, and community giveaways.

**Live at:** [cskafantv.com](https://cskafantv.com)

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Livewire, Blade, Tailwind CSS 4 |
| Admin Panel | Filament 3.3 |
| Canvas/Graphics | Konva.js (tactic board) |
| Build Tool | Vite 6 |
| Database | MySQL (prod), SQLite (dev/test) |
| Auth | Laravel Sanctum, Socialite (Google OAuth) |
| External APIs | LiveScore API, YouTube API, Pusher |

## Features

- **Match Center** — upcoming/past matches, live scores, YouTube embeds for 11 content types
- **Predictions** — users predict match scores, earn points (1pt correct result, +2pts exact score)
- **Player Ratings** — community-driven match ratings, monthly awards
- **Tactic Board** — interactive formation builder with drag-and-drop, drawing tools, PNG export
- **OBS Overlay** — real-time match timer and score overlay for live streaming
- **Video Library** — categorized YouTube video archive
- **League Standings** — synced from LiveScore API
- **Card Tracker** — red/yellow card statistics
- **Hall of Fame** — top-rated players
- **Giveaways** — random draw system for community events
- **Admin Panel** — full CRUD management via Filament at `/admin`

## Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL (or SQLite for quick local setup)

### Installation

```bash
# Clone and install dependencies
git clone <repo-url> && cd football-site
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure database in .env (MySQL or keep SQLite default)
# DB_CONNECTION=mysql
# DB_DATABASE=cskafantv
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Link storage for uploads
php artisan storage:link
```

### Development

```bash
# Start everything (server, queue, logs, vite) concurrently
composer dev

# Or run individually:
php artisan serve        # Laravel dev server
npm run dev              # Vite dev server with HMR
php artisan queue:listen # Process queued jobs
```

The app will be available at `http://localhost:8000`.

### Build for Production

```bash
npm run build
```

## Key Commands

```bash
composer test                                    # Run test suite
./vendor/bin/pint                                # PHP code style fixer
php artisan predictions:calculate-points         # Score predictions for finished matches
php artisan generate:sitemap                     # Regenerate sitemap.xml
php artisan player:save-monthly-award            # Calculate player of the month
php artisan sync:team-external-ids               # Sync team IDs from LiveScore API
```

## Environment Variables

Standard Laravel variables plus:

| Variable | Purpose |
|----------|---------|
| `GOOGLE_CLIENT_ID` / `SECRET` / `REDIRECT_URI` | Google OAuth login |
| `LIVESCORE_API_KEY` / `SECRET` | LiveScore standings sync |
| `YOUTUBE_API_KEY` | YouTube video integration |
| `RECAPTCHA_SITE_KEY` / `SECRET_KEY` | Contact form protection |
| `PUSHER_APP_ID` / `KEY` / `SECRET` | Real-time broadcasting |

## Project Structure

```
app/
  Livewire/Pages/         # 15 page components
  Livewire/Components/    # 9 reusable UI components
  Filament/Resources/     # 10 admin panel resources
  Models/                 # 16 Eloquent models
  Services/               # LiveScoreService (external API)
  Console/Commands/       # Artisan commands
resources/
  views/                  # Blade templates
  js/app.js               # Main JS (tactic board with Konva.js)
  css/app.css             # Tailwind CSS entry point
routes/
  web.php                 # Web routes
  api.php                 # API routes (/api/players)
```

## License

MIT
