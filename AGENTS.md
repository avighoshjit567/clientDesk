# AGENTS.md - ClientDesk

This repository is for **ClientDesk**, a multi-tenant SaaS CRM focused on **real estate companies** and **short-to-medium sales teams**.

## Product intent

ClientDesk is being built as a practical CRM for teams that need:
- lead capture and assignment
- call and follow-up workflows
- visit scheduling
- deal / booking pipeline management
- reporting for managers and tenant admins
- SaaS tenancy with a platform-level super admin

This is not a generic all-in-one ERP. Keep the scope CRM-first.

## Core stack

- Laravel
- Inertia
- Vue 3
- Tailwind CSS
- shadcn-vue
- MySQL
- Redis later for queue/cache
- Laravel Boost for AI-friendly development

## Architecture rules

- Single Laravel monolith
- Multi-tenant isolation via `tenant_id`
- One shared application, not separate codebases per tenant
- Super admin is platform-level only
- Tenant users must never access another tenant's data
- Use MySQL as the default database engine

## Tenancy rules

Every tenant-owned table must include `tenant_id`.

Examples:
- leads
- lead_sources
- projects
- properties
- deals
- tasks
- follow_ups
- activities
- notes
- audit_logs

Do not build features that bypass tenant scoping.

## Role model

Platform role:
- super_admin

Tenant roles:
- tenant_admin
- manager
- sales_rep
- telecaller

## Backend rules

- Use Form Requests for validation
- Use Policies for authorization
- Keep controllers thin
- Prefer Services / Actions for business logic
- Prefer enums or centralized constants for status values
- Avoid giant god controllers
- Mutating routes must never use GET
- When authenticated context exists, do not trust client-supplied tenant/user ownership fields

## Frontend rules

- Use Vue 3 with Inertia only
- Use Tailwind + shadcn-vue primitives
- Prefer reusable tables, filters, dialogs, and form components
- Avoid huge page components with mixed responsibilities
- Keep dashboard widgets and CRM modules composable

## Security rules

- Enforce authorization everywhere
- Log sensitive state changes
- Rate-limit authentication and import endpoints
- Sanitize uploads and imported data
- Add audit logging for admin-sensitive actions
- Never leak tenant data across boundaries

## Data model direction

Planned core modules:
- platform / tenants / plans
- users / invitations / roles
- lead sources / leads / tags
- projects / properties
- pipelines / pipeline stages / deals
- activities / notes / tasks / follow ups / call logs / site visits
- audit logs / reporting support

## AI-friendly development rules

- Laravel Boost should be installed in dev dependencies
- Keep docs current when architecture changes
- Do not invent architecture inconsistent with tenant isolation
- Do not bypass policies for convenience
- Prefer incremental phase-based delivery
- When adding a feature, update the related migration, model, policy, request, controller/service, and UI flow together

## Delivery workflow

- Initial project setup may go directly to `main` by user request
- After initial setup, implement features phase by phase using branches and pull requests
- Keep PRs focused and reviewable
- Prefer small vertical slices over giant batches

## Phase plan summary

- Phase 0: foundation and project setup
- Phase 1: platform core, super admin, tenants, onboarding, roles
- Phase 2: CRM core, leads, lead sources, assignments, notes, tasks, follow ups
- Phase 3: real estate modules, projects, properties, visits, deals, pipeline
- Phase 4: reporting, automations, integrations, billing hardening

If a future change conflicts with this file, pause and update the design deliberately instead of drifting by accident.
