# Access Control Foundation

This slice establishes the first real access-control layer for ClientDesk.

## Included

- Spatie Laravel Permission installed and published
- seeded permission registry and role definitions
- `HasRoles` enabled on the user model
- super admin middleware and dashboard
- tenant admin middleware and dashboard
- dashboard auth flow now redirects by user context:
  - `super_admin` -> super admin dashboard
  - `tenant_admin` / `manager` -> tenant admin dashboard
  - workspace users -> CRM dashboard
  - users without workspace -> onboarding
- sidebar navigation adapts to the authenticated context

## Current role model

Global / platform role:
- `super_admin`

Workspace roles:
- `tenant_admin`
- `manager`
- `sales_rep`
- `telecaller`

## Notes

This is the foundation, not the final authorization system. The tenant membership pivot still remains the main source of truth for current-workspace role checks. Spatie Permission is now present so future policy and permission wiring can build on top of it instead of starting from scratch.
