# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Gold Seeds (sotka-sem.ru)** — Laravel 10 B2B e-commerce platform for a seeds marketplace. Runs on Open Server Panel (Windows) at `sotka.loc` for local development.

## Common Commands

```bash
# Install dependencies
composer install
npm install

# Build frontend assets
npm run dev          # development build
npm run watch        # watch mode with auto-rebuild
npm run prod         # production build (minified)

# Run tests
php artisan test                          # all tests
php artisan test --testsuite=Unit         # unit tests only
php artisan test --testsuite=Feature      # feature tests only
php artisan test --filter=TestClassName   # single test class

# Artisan
php artisan serve                         # local dev server
php artisan migrate                       # run migrations
php artisan cache:clear                   # clear cache
php artisan route:list                    # list all routes

# Search index
php artisan scout:import "App\Models\Product"
```

## Architecture

### Dual Admin System
The project uses **two admin frameworks simultaneously**:
- **Orchid Platform v14** (`/admin`) — Modern admin dashboard, actively developed. Screens in `app/Orchid/Screens/`, layouts in `app/Orchid/Layouts/`, routes in `routes/platform.php`.
- **Voyager v1.6** — Legacy CMS admin panel. Still active but being replaced by Orchid.

The `User` model extends Voyager's user class and implements Orchid's RBAC interfaces. Permissions sync on login via `SyncOrchidPermissionsOnLogin` listener.

### Backend Structure
- **Controllers** (`app/Http/Controllers/`) — HomeController, ProductController, PreorderController, CartController, OrderController, ProfileController, etc.
- **Models** (`app/Models/`) — ~49 Eloquent models. Key: `Product`, `Category`, `Brands`, `Order`, `Preorder`, `PreorderProduct`, `PreorderCheckout`, `UserSaleSystem`, `UserBrandSaleSystem`.
- **Services** (`app/Services/`) — Business logic: PreorderService, OrderService, UserService, ElasticsearchService, TotalsService, PageParser.
- **Jobs** (`app/Jobs/`) — Async processing: ProcessImportJob, ProcessUpdateJob, ParsePreorderFileJob, SendImportReport.
- **Exports/Imports** (`app/Exports/`, `app/Imports/`) — Laravel Excel (Maatwebsite) for Excel/PDF export/import.

### Routes
- `routes/web.php` (~13,500 lines) — All public/authenticated web routes: products, cart, profile, preorder, import/export, Voyager admin.
- `routes/platform.php` — Orchid admin routes: Users, Roles, Managers CRUD with breadcrumbs.
- `routes/api.php` — Minimal API (authenticated user endpoint only).

### Frontend
- **Build tool:** Laravel Mix 5 (Webpack). Single entry: `resources/js/common.js` → `public/js/common.js`.
- **Stack:** Bootstrap 4, jQuery 3.5, Vue.js 2 (limited usage), Swiper 4 (carousels), vanilla-lazyload.
- **Views:** Blade templates in `resources/views/` organized by feature (home, products, profile, preorder, cart, export, email, orchid).

### Search
Laravel Scout with three available drivers:
- **Elasticsearch** (primary, via `elastic-scout-driver`)
- **Algolia** (alternative)
- **TNT Search** (local fallback)

The `Product` model uses the `Searchable` trait with a custom `toSearchableArray()`.

### Key Business Domains
- **Products/Catalog** — Categories (hierarchical), Brands, product specifications, filters.
- **Cart** — Session-based shopping cart with AJAX operations.
- **Orders** — Order creation, history, PDF/Excel export.
- **Preorders** — Separate preorder campaigns with their own cart, products, checkout, and reporting.
- **User Discounts** — Per-category (`UserSaleSystem`) and per-brand (`UserBrandSaleSystem`) discount systems.
- **Managers** — Manager assignment and management module.

### Database
- **MySQL** (InnoDB), database name `seeds`.
- PHPUnit configured to use the same MySQL connection (SQLite in-memory is commented out in `phpunit.xml`).

## Code Style
- **PHP:** Laravel preset via StyleCI. Rule `no_unused_imports` is disabled.
- **Indentation:** 4 spaces (2 for YAML). UTF-8, LF line endings.
- **Language:** UI and some code comments are in Russian.

## Important Notes
- The `.env.example` contains production credentials — do not commit changes to it without sanitizing.
- The `orchid` branch is the active development branch for admin dashboard features.
- Orchid screens follow the pattern: Screen class defines `query()`, `commandBar()`, and `layout()` methods.
- Orchid layouts are in `app/Orchid/Layouts/` (table layouts, filter layouts, etc.).
- Orchid presenters in `app/Orchid/Presenters/` define how models display in admin.
