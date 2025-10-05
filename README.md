# Nimbus Dashboard
Version 1.0 Alpha Preview

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

### Admin Panel (Superuser only)
- User management (add, edit, delete)
- Package assignment
- Quote management (max 20 quotes)
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
```

### Database Tables
- **users**: User profiles, cPanel credentials, API tokens
- **quotes**: Motivational quotes with images

## Security Features
- CSRF token protection on all forms
- API token-based cPanel authentication
- Session management
- SQL prepared statements
- HTML output escaping

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
- Admin (Manage Users, Manage Quotes)

## Version History
- **1.0 Alpha Preview**: Initial release with core functionality

## Credits
Powered by Drift Nimbus
Lead Developer by Chamu Mararike (https://www.linkedin.com/in/chamumararike)
© 2025 Drift Nimbus. All rights reserved.
