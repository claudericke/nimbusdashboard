# Tasks for Dashboard Overview

## Phase 1: Planning & Research (Completed)
- [x] 1.1 Initialize the spec folder and `spec.md` for the Dashboard Overview.
- [x] 1.2 Research existing code for Dashboard widgets (Server status, disk usage, etc.).
- [x] 1.3 Identify third-party service integration points (Trello, Zoho).
- [x] 1.4 Baseline CSS review for glassmorphism suitability.

## Phase 2: Implementation

### Backend & Service Layer (Completed)
#### Task Group 1: Service Enhancements & Trello Integration
**Dependencies:** None

- [x] 1.0 Enhance service layer classes
  - [x] 1.1 Extend `TrelloService` with `getOpenTickets()` to fetch cards from predefined lists.
  - [x] 1.2 Verify `ZohoService::getInvoices()` for data retrieval.
  - [x] 1.3 Update `CpanelService::uapiCall()` for better error handling and `whmCall()` for new account creation.
- [x] 1.4 Verify service functionality with separate test scripts.

### Logic & Controller Layer (Completed)
#### Task Group 2: Dashboard Data & Onboarding Flow
**Dependencies:** Task Group 1

- [x] 2.0 Complete controller logic
  - [x] 2.1 Write manual verification steps for the "Add User" and onboarding email flow.
  - [x] 2.2 Update `DashboardController::index` to include Trello and Zoho data in the view array.
  - [x] 2.3 Implement the "Add User" logic in `AdminController` or a new dedicated controller.
    - Logic should: Call cPanel API -> Create User -> Send `onboardingEmail.md` via `PHPMailer`.
  - [x] 2.4 Implement the Trello "Close Ticket" action endpoint in `TicketController`.
  - [x] 2.5 Verify controller data reaches the view and onboarding emails are sent successfully.

### Frontend & UI Layer (Completed)
#### Task Group 3: Bento Grid UI & Animations
**Dependencies:** Task Group 2

- [x] 3.0 Implement Frontend & UI Layer
  - [x] 3.1 Review and update `public/css/style.css` to include the Bento grid refinement tokens and glassmorphism.
  - [x] 3.2 Refactor `views/dashboard/index.php` to use the new Bento grid layout and animations.
  - [x] 3.3 Implement the "Harare/Zimbabwe" default time widget with real-time JS updates.
  - [x] 3.4 Integrate tile entry animations and hover glow/scaling effects.
  - [x] 3.5 Ensure overall layout is responsive and aesthetic.

## Phase 3: Verification (Completed)
- [x] Verify all widgets display correct data.
- [x] Test "Mark Complete" action for Trello.
- [x] Verify user creation and onboarding email delivery (manual).
- [x] Final visual check for glassmorphism and responsiveness.
