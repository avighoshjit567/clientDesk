# Phase 2 Tasks and Follow-Ups

This slice adds day-to-day CRM execution tools on top of leads.

## Included

- tenant-scoped `tasks` table
- tenant-scoped `follow_ups` table
- task and follow-up models
- task priority, task status, and follow-up status enums
- task and follow-up creation requests and controllers
- `/tasks` page with:
  - task stats
  - task creation form
  - follow-up creation form
  - task list
  - upcoming follow-up list
- sidebar and dashboard updates to surface task activity
- feature tests for tenant-scoped task and follow-up access and creation

## Why this matters

Once leads exist, the next operational need is execution. Sales teams need to know who should call, what needs to happen next, and when follow-ups are due.

## Good next steps

1. notes and call logs
2. task status updates and filters
3. lead detail pages with timeline history
