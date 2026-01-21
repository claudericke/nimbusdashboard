# Specification: Dashboard Overview

## Goal
The goal is to create a modern, "Bento" style dashboard with glassmorphism and animated transitions on actions overview that provides users with a simplified, unified view of their hosting status, support tickets, and billing information, featuring smooth animations and real-time data integration.

## User Stories
- As a hosting client, I want to see my server status and disk usage at a glance so I know my website is healthy.
- As a business owner, I want to see my support tickets and invoices in one place so I don't have to navigate multiple portals.

## Specific Requirements

**Unified Bento Grid Layout**
- Refresh the existing `bento-grid` layout with a more premium aesthetic using Bootstrap 5 and custom CSS.
- Ensure all tiles are responsive and maintain a cohesive "glassmorphism" or "dark/modern" theme as per product vision.

**Widget Integration**
- **Server Status**: Real-time check via `CpanelService::checkServerStatus()`.
- **Active Package**: Display user's hosting plan name.
- **Weather & Time**: Display local weather via `WeatherService` and user-specific local time (Harare/Zimbabwe default, but adaptive).
- **Disk Usage**: Visual representation of `megabytes_used` vs `quota` from `CpanelService::getDiskUsage()`.
- **SSL Protection**: Quick status indicator and link to Manage Certificates view.
- **Inspirational Quote**: Random quote from `Quote` model with background image.

**Trello Support Integration**
- Admin-specific widget to view open Trello cards (tickets) from the support board.
- Ability to "close" (mark as complete or move to a specific list) a ticket directly from the dashboard.

**Quick Action: Add User & Onboarding**
- Dashboard shortcut to the "Add User" interface.
- Must capture user details and trigger cPanel user/email creation.
- Logic must send the `onboardingEmail.md` template upon successful creation.

**Zoho Billing Overview**
- Mini-widget showing recent invoices fetched via `ZohoService::getInvoices()`.

**Animation & UX**
- Implement entry animations for bento tiles and hover effects for interactivity.

## Existing Code to Leverage

**Controllers & Services**
- `DashboardController`: Handles the primary data fetching logic.
- `CpanelService`: Core methods for Disk, SSL, and server checks exist.
- `WeatherService`: Fetches current weather and icons.
- `TrelloService`: Methods for `getCards` and `markCardComplete` are ready.
- `ZohoService`: `getInvoices` method is implemented.

**Models & Views**
- `Quote`: Already provides random quotes for the dashboard.
- `views/dashboard/index.php`: Current structure provides the baseline `bento-grid` and card logic.
- `views/layouts/`: Existing `header.php`, `sidebar.php`, and `footer.php` should be used for consistency.

## Out of Scope
- Full migration of all cPanel functions (focus only on simplified dashboard).
- Complex CRM features beyond basic Trello/Zoho sync.
- Redesign of the entire sidebar/navigation (strictly focusing on the main dashboard content).
