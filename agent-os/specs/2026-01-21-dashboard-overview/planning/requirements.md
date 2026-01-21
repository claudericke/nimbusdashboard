# Spec Requirements: Dashboard Overview

## Initial Description
The user wants to create a visual interface that simplifies core cPanel functions, giving users access to easy support functions such as updating password, adding email accounts to their domains, and viewing various information about their hosting package under Drift Nimbus. Admins have access to all domains and can add and remove domain users across accounts.

## Requirements Discussion

### Functionality Summary
- **Dashboard Widgets**: Show Server Status, Active Package, Weather, Current Time (User Timezone Specific), Disk Usage, SSL protection, and an inspirational quote.
- **Add Users**: Function to add cPanel API connections and users. This must immediately email the users with details (using `onboardingEmail.md`).
- **Trello Integration**: View and close open Trello tickets (Admin/Nimbus Admin only).
- **Email Management**: Simplified interface for managing email accounts.
- **Domain Management**: Viewing and managing domains.
- **Billing Management**: Integration with Zoho API for billing information.

### Tech Stack Implementation
- **Framework**: Custom PHP MVC
- **Frontend**: Bootstrap 5, Lucide Icons, Google Fonts (Inter).
- **Animations**: Framer Motion mentioned (will use CSS/JS equivalent for PHP environment unless React is introduced).

## Requirements Summary

### Functional Requirements
- Bento-grid style layout for the dashboard.
- Real-time/Fetched data for widgets.
- Integration with cPanel API for user/domain/email management.
- Integration with Trello API for support tickets.
- Integration with Zoho API for billing.
- Automated email trigger on user creation.

### Reusability Opportunities
- Existing MVC structure (Controllers, Models, Views).
- Existing `onboardingEmail.md` template.
- Existing PHP API logic (if any) in the codebase.

### Scope Boundaries
**In Scope:**
- Main Dashboard view with stipulated widgets.
- Email and Domain listing/management views.
- User creation and onboarding email flow.
- Trello and Zoho API integrations.
- Admin view for cross-domain management.

**Out of Scope:**
- Full cPanel replacement (only core functions are simplified).
- Complex CRM or non-hosting related features.

### Technical Considerations
- User timezone-specific time display.
- Secure API key management for cPanel, Trello, and Zoho.
- Mobile responsiveness using Bootstrap.
