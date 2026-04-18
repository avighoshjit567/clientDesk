# Phase 1 Platform Core

This phase starts the SaaS foundation for ClientDesk.

## Included in this slice

### New core tables
- `subscription_plans`
- `tenants`
- `tenant_settings`
- `tenant_user`
- `tenant_invitations`

### User model extensions
- `platform_role`
- `phone`
- `current_tenant_id`

## Why this comes first

Before building leads, follow-ups, projects, or deals, the app needs a clear platform shape:
- what a tenant is
- how users belong to tenants
- how invitations work
- how plans are represented
- how current tenant context is selected

## Design choices

- tenancy is shared-database, scoped by `tenant_id`
- super admin remains platform-level
- tenant membership is many-to-many via `tenant_user`
- tenant settings are stored separately to keep the tenant record clean
- invitations are explicit records instead of overloaded user rows

## Next likely slice

After this, the next PR can build one of these:
1. tenant onboarding flow
2. roles and permissions integration
3. lead management foundation
