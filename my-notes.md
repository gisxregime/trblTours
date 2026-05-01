# TribalTours File System Notes

Updated: April 26, 2026

## 1) Root-Level Overview

Main folders in the project root:

- `app/` -> Core PHP application code (controllers, models, services, Livewire components).
- `bootstrap/` -> Laravel app bootstrapping and cached bootstrap files.
- `config/` -> Framework and app configuration files.
- `database/` -> Migrations, factories, seeders, and local sqlite DB files.
- `public/` -> Public web root (compiled assets, images, favicon, entry `index.php`).
- `resources/` -> Frontend source files (Blade views, CSS, JS).
- `routes/` -> Route definitions (`web.php`, `auth.php`, etc.).
- `storage/` -> Logs, cache, compiled Blade views, framework runtime files.
- `tests/` -> Feature, Unit, and Browser tests.
- `vendor/` -> Composer dependencies.

Important root files:

- `artisan` -> Laravel CLI entry point.
- `composer.json` -> PHP dependencies/scripts.
- `package.json` -> Frontend dependencies/scripts.
- `vite.config.js` -> Vite build config.
- `tailwind.config.js` -> Tailwind config.

## 2) Where Pages Are Defined

### Route Files

- `routes/web.php` -> Main site and dashboard routes.
- `routes/auth.php` -> Login/signup/password/verification routes.

### Public Pages (from `routes/web.php`)

- `/` -> view `resources/views/welcome.blade.php`
- `/explore-tours` -> view `resources/views/explore-tours.blade.php`

### Dashboard Pages (from `routes/web.php`)

- `/dashboard` -> `DashboardController@redirect` (role-based redirect)
- `/dashboard/tourist` -> `DashboardController@tourist`
- `/dashboard/guide` -> `DashboardController@guide`
- `/dashboard/admin` -> `DashboardController@admin`

### Auth Pages (from `routes/auth.php`)

- `/login` -> `AuthenticatedSessionController@create` -> `resources/views/auth/login.blade.php`
- `/guide-login` -> guide login route using same login view
- `/signup` and multi-step signup routes -> `resources/views/auth/signup/flow.blade.php`
- `/forgot-password`, `/reset-password/{token}`, `/verify-email`, OTP routes -> views under `resources/views/auth/`

## 3) View Files Location

Main Blade folders under `resources/views/`:

- `welcome.blade.php` -> Home page
- `explore-tours.blade.php` -> Explore Tours page shell
- `dashboards/` -> Tourist, guide, admin dashboard pages
- `auth/` -> Auth pages (login, register, OTP, reset, verify)
- `profile/` -> Profile edit page and partials
- `components/` -> Reusable Blade UI components
- `livewire/` -> Blade templates used by Livewire components
- `emails/` -> Email templates
- `layouts/` -> Shared page layouts

## 4) Livewire Structure

Livewire PHP classes in `app/Livewire/`:

- `ExploreToursFeed.php`
- `TouristNotifications.php`
- `Guide/` folder with guide-related components

Matching Livewire Blade templates in `resources/views/livewire/`:

- `explore-tours-feed.blade.php`
- `tourist-notifications.blade.php`
- `guide/*.blade.php`

## 5) Backend Structure

### Controllers

Located in `app/Http/Controllers/`:

- `DashboardController.php`
- `BookingController.php`
- `RequestController.php`
- `FeedController.php`
- `ProfileController.php`
- `Auth/` subfolder for authentication controllers

### Models

Located in `app/Models/`:

- Core entities include `User.php`, `Tour.php`, `Booking.php`, `BookingRequest.php`, `Message.php`, `Conversation.php`, and related guide/tour models.

### Notifications and Mail

- Notifications: `app/Notifications/`
- Mail classes: `app/Mail/`

## 6) Database Layer

In `database/`:

- `migrations/` -> Schema history and table definitions
- `factories/` -> Test/development fake data generators
- `seeders/` -> Seed scripts
- `database.sqlite` -> Local sqlite DB
- `database_dusk.sqlite` -> Browser test DB

## 7) Frontend Assets

Source assets:

- `resources/css/`
- `resources/js/`

Public/static assets:

- `public/images/`
- `public/hero/`
- `public/favicon.ico`, `public/favicon.png`

Compiled build output:

- `public/build/`

## 8) Testing Structure

In `tests/`:

- `Feature/` -> End-to-end request/response behavior tests
- `Unit/` -> Small isolated logic tests
- `Browser/` -> Dusk browser automation tests
- `Pest.php` -> Pest test bootstrap/config

## 9) Quick Mental Model

Use this flow when tracing any page:

1. Find URL in `routes/web.php` or `routes/auth.php`
2. If route uses a controller, open the method in `app/Http/Controllers/`
3. Locate returned Blade file in `resources/views/`
4. If page includes `<livewire:...>`, open component class in `app/Livewire/` and template in `resources/views/livewire/`
5. Trace data source from models in `app/Models/` and schema in `database/migrations/`
