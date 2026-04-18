# ClientDesk Phase Plan

## Phase 0 - Foundation

Goal: establish the project baseline.

Includes:
- official Laravel + Vue starter foundation
- Inertia + Vue + Tailwind + shadcn-vue baseline
- MySQL-first environment defaults
- Laravel Boost added to dev dependencies
- AGENTS.md for future AI and developer guidance
- repo and CI baseline

## Phase 1 - Platform Core

Goal: make the SaaS skeleton real.

Includes:
- super admin access model
- tenants
- subscription plans
- tenant settings
- tenant invitations
- role and permission design
- tenant onboarding flow
- tenant-scoped middleware / policies baseline

## Phase 2 - CRM Core

Goal: usable daily CRM for a sales team.

Includes:
- lead sources
- leads
- assignment flow
- notes
- tasks
- follow ups
- call logs
- basic dashboard widgets
- audit log baseline

## Phase 3 - Real Estate Workflow

Goal: support the target vertical properly.

Includes:
- projects
- properties / units
- visit scheduling
- deal pipeline
- pipeline stages
- booking / won-lost flow
- manager visibility and operational reporting

## Phase 4 - SaaS Hardening and Growth

Goal: push the app toward industry-standard quality.

Includes:
- deeper reporting
- automation rules
- import tools
- billing implementation
- activity monitoring
- API / webhook groundwork
- integration strategy for calling / WhatsApp / messaging

## Delivery rule

- Initial setup goes to `main`
- After that, each phase should be delivered as focused branches and PRs
- Keep changes incremental and vertically sliced where possible
