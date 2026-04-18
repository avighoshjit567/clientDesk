# ClientDesk

ClientDesk is a multi-tenant SaaS CRM being built for **real estate companies** and **short-to-medium sales teams**.

## Planned stack

- Laravel
- Inertia
- Vue 3
- Tailwind CSS
- shadcn-vue
- MySQL
- Laravel Boost

## Current status

This repository is currently at **Phase 0, foundation setup**.

The base application comes from the official Laravel Vue starter kit, then gets adapted for ClientDesk's CRM and tenancy plan.

## Initial architecture direction

- single Laravel monolith
- super admin at platform level
- tenant admins and tenant users inside isolated workspaces
- tenant-owned business data scoped by `tenant_id`
- MySQL as the default database engine

## Development workflow

- Initial setup is committed to `main`
- After that, features will be built **phase by phase** on branches and reviewed through pull requests

## AI-friendly development

This project is being prepared for AI-assisted development.

That means:
- `AGENTS.md` contains project rules and architecture instructions
- Laravel Boost is included in dev dependencies
- future changes should stay aligned with tenancy, authorization, and incremental delivery rules

## Project planning docs

- `AGENTS.md` - architecture, coding, tenancy, and delivery rules
- `docs/PHASES.md` - phase-by-phase roadmap

## Local setup

When PHP and Composer are available:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan boost:install
```

For repeatable local validation on this host, you can also run:

```bash
./scripts/validate-local.sh
```

That script uses SQLite for local validation so migrations, tests, type-checking, lint, and build can be run in one command even when MySQL is not being used locally.

## Environment defaults

- database: MySQL
- app name: ClientDesk

## Notes

The repository was initially bootstrapped before PHP and Composer were available on this machine.

That blocker has now been resolved on the current host, and the project has been validated locally with:

- `composer install`
- `php artisan key:generate`
- SQLite-backed `php artisan migrate`
- `php artisan test`
- `npm run types:check`
- `npm run lint:check`
- `npm run build`

This starter kit's frontend build uses Wayfinder generation during Vite build, which calls `php artisan wayfinder:generate --with-form`, so PHP availability is required for full frontend build validation.
