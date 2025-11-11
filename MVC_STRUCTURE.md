# MVC Structure Documentation

## Directory Structure

```
/dashboard
├── index.php                    # Entry point (router)
├── index.legacy.php             # Original monolithic file (backup)
├── autoload.php                 # Class autoloader
├── .env                         # Environment variables
├── .htaccess                    # URL rewriting rules
├── composer.json                # PHP dependencies
│
├── /app
│   ├── Router.php               # URL routing system
│   │
│   ├── /Controllers             # Business logic
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── DomainController.php
│   │   ├── EmailController.php
│   │   ├── SslController.php
│   │   ├── BillingController.php
│   │   ├── SettingsController.php
│   │   ├── TicketController.php
│   │   └── AdminController.php
│   │
│   ├── /Models                  # Database operations
│   │   ├── User.php
│   │   ├── Quote.php
│   │   ├── Permission.php
│   │   └── Role.php
│   │
│   ├── /Services                # External API integrations
│   │   ├── CpanelService.php
│   │   ├── ZohoService.php
│   │   ├── TrelloService.php
│   │   ├── WeatherService.php
│   │   └── PaynowService.php
│   │
│   └── /Helpers                 # Utility classes
│       ├── Database.php
│       ├── Session.php
│       ├── CSRF.php
│       └── functions.php
│
├── /config                      # Configuration files
│   ├── database.php
│   ├── cpanel.php
│   ├── zoho.php
│   └── trello.php
│
├── /routes                      # Route definitions
│   └── web.php
│
├── /views                       # HTML templates
│   ├── /layouts
│   │   ├── header.php           # TODO
│   │   ├── sidebar.php          # TODO
│   │   └── footer.php           # TODO
│   │
│   ├── /auth
│   │   └── login.php
│   │
│   ├── /dashboard
│   │   └── index.php            # TODO
│   │
│   ├── /domains
│   │   └── index.php            # TODO
│   │
│   ├── /emails
│   │   ├── index.php            # TODO
│   │   ├── create.php           # TODO
│   │   └── modals.php           # TODO
│   │
│   ├── /ssl
│   │   └── index.php            # TODO
│   │
│   ├── /billing
│   │   └── index.php            # TODO
│   │
│   ├── /settings
│   │   └── index.php            # TODO
│   │
│   ├── /tickets
│   │   ├── new.php              # TODO
│   │   ├── open.php             # TODO
│   │   ├── awaiting.php         # TODO
│   │   └── closed.php           # TODO
│   │
│   └── /admin
│       ├── users.php            # TODO
│       ├── quotes.php           # TODO
│       └── permissions.php      # TODO
│
├── /public                      # Publicly accessible files
│   ├── /css
│   │   └── style.css
│   ├── /js
│   │   └── app.js               # TODO
│   └── /assets
│       ├── /images
│       └── /sounds
│
└── /vendor                      # Composer packages
```

## Component Responsibilities

### Entry Point (index.php)
- Load environment variables
- Initialize autoloader
- Start session
- Initialize CSRF protection
- Create router and dispatch requests

### Router (app/Router.php)
- Map URLs to Controller@method
- Handle GET/POST requests
- Support dynamic route parameters
- Execute controller methods

### Controllers (app/Controllers/)
- Handle HTTP requests
- Validate input
- Call services/models
- Return views with data
- Handle redirects

### Models (app/Models/)
- Database CRUD operations
- Data validation
- Business logic related to data
- Return data arrays/objects

### Services (app/Services/)
- External API integrations
- Third-party service communication
- API authentication/token management
- Data transformation

### Helpers (app/Helpers/)
- Utility functions
- Session management
- CSRF protection
- Database connection
- Common functions (h(), redirect(), view(), etc.)

### Views (views/)
- HTML templates
- Display data from controllers
- Include layout components
- Minimal PHP logic (loops, conditionals only)

### Config (config/)
- Configuration arrays
- Environment-based settings
- API credentials
- Service endpoints

## Request Flow

```
1. User Request
   ↓
2. index.php (Entry Point)
   ↓
3. Router (routes/web.php)
   ↓
4. Controller (app/Controllers/)
   ↓
5. Service/Model (app/Services/ or app/Models/)
   ↓
6. View (views/)
   ↓
7. Response to User
```

## Example Request Flow

### Login Request:
```
POST /login
  ↓
index.php loads Router
  ↓
Router matches: POST /login → AuthController@login
  ↓
AuthController@login:
  - Validates CSRF token
  - Gets domain/password from POST
  - Calls User::findByDomain()
  - Calls CpanelService::createToken()
  - Sets session variables
  - Redirects to /dashboard
```

### Dashboard Page:
```
GET /dashboard
  ↓
index.php loads Router
  ↓
Router matches: GET /dashboard → DashboardController@index
  ↓
DashboardController@index:
  - Checks authentication
  - Calls CpanelService::getDiskUsage()
  - Calls WeatherService::getCurrentWeather()
  - Calls Quote::random()
  - Loads view('dashboard/index', $data)
  ↓
views/dashboard/index.php renders HTML
```

## Naming Conventions

### Controllers
- PascalCase with "Controller" suffix
- Example: `EmailController`, `DashboardController`

### Models
- PascalCase, singular noun
- Example: `User`, `Quote`, `Permission`

### Services
- PascalCase with "Service" suffix
- Example: `CpanelService`, `ZohoService`

### Methods
- camelCase, descriptive verbs
- Example: `index()`, `createEmail()`, `changePassword()`

### Views
- lowercase with hyphens or underscores
- Example: `index.php`, `create.php`, `change-password.php`

### Routes
- lowercase with hyphens
- Example: `/emails`, `/admin/users`, `/tickets/open`

## Helper Functions

### h($str)
HTML escape string for XSS protection

### redirect($url)
Redirect to URL and exit

### view($path, $data)
Load view file with data

### asset($path)
Generate asset URL

### env($key, $default)
Get environment variable

## Session Helper

### Session::start()
Start session

### Session::set($key, $value)
Set session variable

### Session::get($key, $default)
Get session variable

### Session::has($key)
Check if session variable exists

### Session::isLoggedIn()
Check if user is logged in

### Session::isSuperuser()
Check if user is superuser

## CSRF Helper

### CSRF::ensureToken()
Generate CSRF token if not exists

### CSRF::field()
Output hidden CSRF input field

### CSRF::check()
Validate CSRF token from POST

## Database Helper

### Database::getInstance()
Get singleton database instance

### Database::getConnection()
Get mysqli connection

### Database::query($sql)
Execute SQL query

### Database::prepare($sql)
Prepare SQL statement

## Best Practices

1. **Controllers should be thin** - Move complex logic to services/models
2. **Views should be dumb** - Minimal PHP, only display logic
3. **Services for external APIs** - All API calls go through services
4. **Models for database** - All database operations go through models
5. **Use helpers** - Don't repeat common operations
6. **CSRF on all forms** - Always use CSRF::field() and CSRF::check()
7. **Validate input** - Always validate and sanitize user input
8. **Escape output** - Always use h() for HTML output
9. **Type hint** - Use type hints for method parameters
10. **Document code** - Add comments for complex logic

## Migration Checklist

- [x] Phase 1: Foundation (Complete)
  - [x] Directory structure
  - [x] Helpers (Database, Session, CSRF)
  - [x] Services (Cpanel, Zoho, Trello, Weather, Paynow)
  - [x] Models (User, Quote, Permission, Role)
  - [x] Router and BaseController
  - [x] Sample controllers (Auth, Dashboard)
  
- [x] Phase 2: Controllers (Complete)
  - [x] DomainController
  - [x] EmailController
  - [x] SslController
  - [x] BillingController
  - [x] SettingsController
  - [x] TicketController
  - [x] AdminController
  
- [ ] Phase 3: Views (In Progress)
  - [ ] Layout components
  - [ ] All page views
  - [ ] Modal components
  
- [ ] Phase 4: Testing (Pending)
  - [ ] Test all pages
  - [ ] Test all features
  - [ ] Fix bugs
  - [ ] Performance optimization
