# Repository Guidelines

## Project Structure & Module Organization
Core domain logic lives in `app/`, including models, Filament resources, HTTP controllers, and policies. Optional ERP features ship as `plugins/<Vendor>/<Module>`; keep each module's migrations, seeders, config, and service provider inside the plugin. Shared configuration resides in `config/`, while UI sources sit in `resources/js`, `resources/css`, and Blade views in `resources/views`. Place database migrations and seeders in `database/`, and register routes under `routes/` to mirror Laravel conventions.

## Build, Test, and Development Commands
Configure dependencies with `composer install` and `npm install`. Run `php artisan erp:install` to migrate, seed, and provision default roles on a fresh instance. Use `composer run dev` for the local HTTP server, queue listener, pail log stream, and Vite watcher. Produce production assets with `npm run build`. When a plugin introduces schema changes, execute `php artisan migrate --seed` to apply migrations and reseed fixtures.

## Coding Style & Naming Conventions
Follow PSR-12 with 4-space indentation. Run `./vendor/bin/pint` before committing to enforce formatting. Name Filament resources, widgets, and pages in StudlyCase (e.g., `InventoryResource`, `ManageOrdersPage`) and align job, event, and policy namespaces with their `app/` counterparts. Export front-end functions in camelCase, and organize shared Tailwind layers in `resources/css/app.css`.

## Testing Guidelines
Place Feature tests in `tests/Feature` and Unit tests in `tests/Unit`, matching the namespaces under test. Use PHPUnit 11 via `php artisan test`; rely on `RefreshDatabase`, factories, and plugin seeders for deterministic fixtures. Add coverage for Filament panel flows and domain services whenever behavior changes.

## Commit & Pull Request Guidelines
Prefix commit messages with repository standards (`Feat:`, `Fix:`, `Chore:`, `Refactor:`) and write imperative subjects. PRs should summarize scope, list manual or automated test results, link related issues, and include UI screenshots when applicable. Call out new migrations, queues, or environment variables so reviewers can plan deployment steps.

## Plugin Development Tips
Ensure each plugin ships an idempotent installer command (`php artisan <plugin>:install`) and documents dependencies in its service provider. Extend core behavior through plugin providers or Filament hooks instead of modifying `app/` directly; use the composer merge plugin to expose overrides cleanly.
