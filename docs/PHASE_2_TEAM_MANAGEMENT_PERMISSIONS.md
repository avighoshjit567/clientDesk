# Phase 2, Team Management and Permission Enforcement

This slice expands ClientDesk beyond raw tenant onboarding and entity CRUD.

## Included

- tenant team management page
- invitation creation and revocation
- authenticated invitation acceptance flow
- tenant member role updates and removal
- permission-aware navigation and page actions
- backend authorization gates and model policies for:
  - leads
  - lead sources
  - tasks
  - follow-ups

## Role behavior in this slice

- `tenant_admin`
  - full workspace management
  - can manage team and invitations
- `manager`
  - can access tenant admin dashboard and team management
  - can manage team and operational CRM modules
- `sales_rep`
  - can work with leads, tasks, and follow-ups
  - cannot manage team
- `telecaller`
  - can work with leads and follow-ups
  - can view tasks, but cannot create new tasks

## Current invitation flow

1. tenant admin creates an invitation from the team page
2. system generates a tokenized invite link
3. invited user signs in with the same email address
4. invited user opens the link and accepts the invitation
5. user is attached to the tenant workspace

## Notes

This is still an MVP-safe implementation. It does not yet send real outbound invitation emails. The link is created and exposed in the UI so delivery can be handled manually for now, and later connected to notifications or email.
