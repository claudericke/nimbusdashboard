# Nimbus Dashboard
Version 1.1.0

## Overview
A modern, dark-themed hosting management dashboard for Drift Nimbus customers. Built with PHP and Bootstrap 5, integrating cPanel UAPI, Zoho Books API, and Paynow payment gateway.

## Features

### Dashboard
- Welcome card with user greeting
- Real-time clock widget (Harare timezone)
- Weather widget with Open-Meteo API integration
- Disk usage monitoring
- SSL status indicator
- Daily motivational quotes

### Domain Management
- View all domains and subdomains
- Direct cPanel access links

### Email Management
- List all email accounts with pagination
- Create new email accounts
- Change email passwords via modal
- Delete email accounts
- Direct link to Nimbus Mail webmail

### SSL Certificates
- View active SSL certificates
- Direct cPanel SSL management links

### Billing & Invoices
- Zoho Books API integration with auto-refresh tokens
- Domain-filtered invoice display
- Invoice status (PAID/UNPAID)
- Paynow payment integration
- Payment success/failure redirect pages

### Settings
- Update profile name
- Set profile picture URL

### Tickets System (Superuser only)
- Trello integration with SUPPORT TEAM board
- View tickets: New, Open, Awaiting Response, Closed
- Table view with labels, dates, and assigned members
- Click ticket to view full details in modal
- Checkbox to close tickets (marks as complete)
- Real-time notifications: 30s polling, sound alert, browser notifications
- Background checking on all pages

### Admin Panel (Superuser only)
- User management (add, edit, delete)
- Package assignment
- Quote management (max 20 quotes)
- Role-based permissions management
- Superuser role assignment

## Technical Stack

### Backend
- PHP 7.4+
- MySQL database
- cPanel UAPI with token authentication
- Zoho Books API v3
- Composer for dependency management

### Frontend
- Bootstrap 5.3.0
- Font Awesome 5.15.3
- Custom dark theme (#1e1e1e)
- Responsive bento grid layout
- Fixed sidebar navigation (260px)

### APIs & Integrations
- **cPanel UAPI**: Email, domains, SSL, disk quota
- **Zoho Books**: Invoice management with OAuth refresh
- **Paynow**: Payment gateway (paynow.co.zw)
- **Open-Meteo**: Weather data
- **Trello**: Support ticket management with real-time polling

## File Structure
```
/dashboard
├── index.php           # Main application file
├── helpers.php         # CSRF protection helpers
├── .env               # Environment configuration
├── composer.json      # PHP dependencies
├── css/
│   └── style.css      # Custom styles
└── vendor/            # Composer packages
```

## Configuration

### Environment Variables (.env)
```
DB_HOST=localhost
DB_USER=your_db_user
DB_PASS=your_db_password
DB_NAME=your_db_name

ZOHO_ORGANIZATION_ID=your_org_id
ZOHO_CLIENT_ID=your_client_id
ZOHO_CLIENT_SECRET=your_client_secret
ZOHO_ACCESS_TOKEN=your_access_token
ZOHO_REFRESH_TOKEN=your_refresh_token

TRELLO_API_KEY=your_trello_api_key
TRELLO_TOKEN=your_trello_token

APP_VERSION=1.2.0
```

### Database Tables
- **users**: User profiles, cPanel credentials, API tokens, user roles
- **quotes**: Motivational quotes with images
- **roles**: User role definitions (superuser, admin, client, viewer)
- **permissions**: Role-based menu access control

## Security Features
- CSRF token protection on all forms
- API token-based cPanel authentication
- Session management
- SQL prepared statements
- HTML output escaping
- Role-based access control (RBAC)

## Session Variables
- `$_SESSION['cpanel_username']` - cPanel username
- `$_SESSION['cpanel_domain']` - User's domain
- `$_SESSION['cpanel_api_token']` - cPanel API token
- `$_SESSION['is_superuser']` - Superuser flag (1/0)
- `$_SESSION['user_role']` - User role (superuser/admin/client/viewer)
- `$userPermissions` - Array of menu permissions loaded from database

## Key Functions

### Authentication
- cpanel_create_token_with_password()`: Generate cPanel API tokens
- uapi_call()`: Execute cPanel UAPI requests

### Zoho Integration
- zoho_refresh_token()`: Auto-refresh expired tokens
- zoho_call()`: API requests with 401 retry logic

### Payment Processing
- Paynow URL generation with Base64 encoding
- Invoice balance tracking
- Payment redirect handling

## Navigation Structure
- Dashboard
- Domains
- Emails (Email Accounts, Add Email, Nimbus Mail)
- SSL Certificates
- Billing
- Settings
- Tickets (New Ticket, Open Tickets, Awaiting Response, Closed Tickets) - Superuser only
- Admin (Manage Users, Manage Quotes, Manage Permissions) - Superuser only

## Version History

### Version 1.2.0 (Current)
- Multilevel user management with role-based permissions
- Roles: superuser, admin, client, viewer
- Permissions management page in Admin panel
- Login simplified: Domain + Password only (username fetched from database)
- Session variables: user_role, user_permissions added
- Trello integration for support tickets (SUPPORT TEAM board)
- Tasks renamed to Tickets with submenus: New Ticket, Open Tickets, Awaiting Response, Closed Tickets
- Ticket table view with columns: Ticket Name, Labels, Start Date, Due Date, Assigned To
- Ticket modal displays card details (description, due date, labels, attachments)
- Checkbox to move tickets from Open to Closed with completion marking
- Real-time ticket notifications: 30-second polling, sound alert (ding.wav), browser notifications
- Background ticket checking works on any page for superusers

### Version 1.1.0
- Email accounts disk usage column showing actual usage in x/y MB format
- Changed cPanel API call from list_pops to list_pops_with_disk for accurate disk data

### Version 1.0.0
- Real-time server status check via cpsrvd (port 2083)
- Email account creation success modal with copy and email functionality
- Password change success modal with copy and email functionality
- Admin user creation success modal with copy and email functionality
- Automated email notifications with Nimbus logo for account details
- Disk usage display converted from MB to GB
- Weather widget with local PNG images and dynamic backgrounds
- Custom background images for dashboard cards (clock, disk, SSL)
- Version number display in footer from .env configuration
- Admin domain switcher for superuser account impersonation
- User profile section in sidebar with profile picture and domain
- Server status and package name display on welcome card

### Version 1.0 Alpha Preview
- Initial release with core functionality
- Dashboard with widgets (weather, time, disk usage, SSL, quotes)
- Domain and email management
- SSL certificate viewing
- Zoho Books invoice integration with Paynow payments
- Admin panel for user and quote management
- Dark-themed sidebar navigation

## Credits
Powered by Drift Nimbus
Lead Developer by Chamu Mararike (https://www.linkedin.com/in/chamumararike)
© 2025 Drift Nimbus. All rights reserved.
