# Phase 2 Leads Foundation

This slice starts the real CRM layer for ClientDesk.

## Included

- tenant-scoped `lead_sources` table
- tenant-scoped `leads` table
- `LeadSource` and `Lead` models
- lead status enum
- tenant-workspace middleware for CRM routes
- leads index page with:
  - basic lead stats
  - lead creation form
  - lead source creation form
  - recent lead table
- automatic default lead sources during tenant onboarding
- feature tests for workspace-scoped lead access and creation

## Why this is the first Phase 2 slice

Leads are the center of the early CRM workflow. Before tasks, follow-ups, or call logs become useful, the system needs a clear place to store incoming prospects and track where they came from.

## Good next steps

1. task and follow-up module
2. notes and call logs
3. lead assignment dashboard widgets and filters
