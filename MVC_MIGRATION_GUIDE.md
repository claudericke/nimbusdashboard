# MVC Migration Guide

## Phase 1: Foundation ✅ COMPLETED

### What Was Done:
1. ✅ Created MVC directory structure
2. ✅ Moved CSS and assets to `/public` folder
3. ✅ Created helper classes:
   - `Database.php` - Singleton database connection
   - `Session.php` - Session management wrapper
   - `CSRF.php` - CSRF token protection
   - `functions.php` - Global helper functions
4. ✅ Created configuration files:
   - `config/database.php`
   - `config/cpanel.php`
   - `config/zoho.php`
   - `config/trello.php`
5. ✅ Created service classes:
   - `CpanelService.php` - All cPanel UAPI operations
   - `ZohoService.php` - Zoho Books API operations
   - `TrelloService.php` - Trello API operations
   - `WeatherService.php` - Weather API operations
6. ✅ Created model classes:
   - `User.php` - User database operations
   - `Quote.php` - Quote database operations
   - `Permission.php` - Permission database operations
7. ✅ Created routing system:
   - `Router.php` - URL routing class
   - `routes/web.php` - Route definitions
   - `BaseController.php` - Base controller with common methods
8. ✅ Created sample controllers:
   - `AuthController.php` - Authentication logic
   - `DashboardController.php` - Dashboard logic
9. ✅ Created autoloader and new entry point
10. ✅ Backed up original `index.php` to `index.legacy.php`

### File Changes:
- **Moved**: `css/` → `public/css/`
- **Moved**: `assets/` → `public/assets/`
- **Backed up**: `index.php` → `index.legacy.php`
- **Created**: New minimal `index.php` as router entry point
- **Created**: `.htaccess` for URL rewriting

## Phase 2: Complete Controller Migration ✅ COMPLETED

### Controllers Created:
1. ✅ `DomainController.php` - Domain management
2. ✅ `EmailController.php` - Email account management
3. ✅ `SslController.php` - SSL certificate viewing
4. ✅ `BillingController.php` - Invoice and payment management
5. ✅ `SettingsController.php` - User settings
6. ✅ `TicketController.php` - Trello ticket management
7. ✅ `AdminController.php` - Admin panel (users, quotes, permissions)
8. ✅ `PaynowService.php` - Payment gateway service
9. ✅ `Role.php` - Role model

### How to Migrate Each Controller:

#### Example: EmailController
1. Extract email-related logic from `index.legacy.php`
2. Create methods: `index()`, `create()`, `store()`, `changePassword()`, `delete()`
3. Use `CpanelService` for API calls
4. Return views with data

```php
class EmailController extends BaseController {
    private $cpanelService;
    
    public function __construct() {
        $this->cpanelService = new CpanelService();
    }
    
    public function index() {
        $this->requireAuth();
        $page = $_GET['page'] ?? 1;
        $emails = $this->cpanelService->getEmails($page);
        $this->view('emails/index', ['emails' => $emails]);
    }
}
```

## Phase 3: View Extraction (AFTER CONTROLLERS)

### Views to Create:
1. `views/layouts/header.php` - Common header
2. `views/layouts/sidebar.php` - Navigation sidebar
3. `views/layouts/footer.php` - Common footer
4. `views/dashboard/index.php` - Dashboard page
5. `views/domains/index.php` - Domains page
6. `views/emails/index.php` - Email listing
7. `views/emails/create.php` - Create email form
8. `views/ssl/index.php` - SSL certificates
9. `views/billing/index.php` - Invoices
10. `views/settings/index.php` - Settings page
11. `views/tickets/*.php` - Ticket pages
12. `views/admin/*.php` - Admin pages

### View Structure Pattern:
```php
<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <!-- Page content here -->
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
```

## Phase 4: Testing

### Test Each Page:
- [ ] Login/Logout
- [ ] Dashboard widgets
- [ ] Domain listing
- [ ] Email CRUD operations
- [ ] SSL certificates
- [ ] Billing/Invoices
- [ ] Settings update
- [ ] Tickets (superuser)
- [ ] Admin panel (superuser)

### Test Features:
- [ ] CSRF protection
- [ ] Session management
- [ ] Role-based permissions
- [ ] API integrations (cPanel, Zoho, Trello)
- [ ] Real-time ticket notifications
- [ ] Mobile responsive sidebar

## How to Switch Between Old and New

### Use New MVC Version:
Current setup - `index.php` is the new MVC entry point

### Revert to Old Version:
```bash
# Backup new index.php
copy index.php index.mvc.php

# Restore old version
copy index.legacy.php index.php
```

## URL Changes

### Old URLs (Query String):
- `index.php?page=dashboard`
- `index.php?page=emails`
- `index.php?page=admin&sub=users`

### New URLs (Clean URLs):
- `/dashboard`
- `/emails`
- `/admin/users`

## Asset Path Changes

### Old Paths:
```html
<link rel="stylesheet" href="css/style.css">
<img src="assets/images/logo.png">
```

### New Paths:
```html
<link rel="stylesheet" href="/public/css/style.css">
<img src="/public/assets/images/logo.png">
```

## Next Steps

1. **Create remaining controllers** (EmailController, DomainController, etc.)
2. **Extract views** from `index.legacy.php` into separate view files
3. **Test each page** thoroughly
4. **Update documentation** with any changes
5. **Remove `index.legacy.php`** once migration is complete

## Benefits Achieved

✅ **Separation of Concerns** - Logic, data, and presentation are separated
✅ **Reusable Services** - API services can be used across controllers
✅ **Clean URLs** - SEO-friendly URLs without query strings
✅ **Easier Testing** - Each component can be tested independently
✅ **Better Organization** - Code is organized by feature/responsibility
✅ **Scalability** - Easy to add new features without touching existing code
✅ **Maintainability** - Bugs are easier to locate and fix

## Current Status

**Phase 1: COMPLETE** ✅
- Foundation is ready
- Services are functional
- Models are ready
- Routing system is working
- Sample controllers created

**Phase 2: COMPLETE** ✅
- All controllers created
- All business logic extracted from `index.legacy.php`
- PaynowService added
- Role model added

**Phase 3: IN PROGRESS** 🔄
- Need to extract views
- Need to create layout components

**Phase 4: PENDING** ⏳
- Testing required after Phase 3 complete
