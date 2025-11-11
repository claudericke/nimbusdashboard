# Developer Quick Reference Guide

## Adding a New Feature

### 1. Create a Route
**File**: `routes/web.php`
```php
$router->get('/my-feature', 'MyFeatureController@index');
$router->post('/my-feature/save', 'MyFeatureController@save');
```

### 2. Create a Controller
**File**: `app/Controllers/MyFeatureController.php`
```php
<?php

class MyFeatureController extends BaseController {
    public function index() {
        $this->requireAuth();
        $this->view('my-feature/index', ['data' => $data]);
    }

    public function save() {
        $this->requireAuth();
        CSRF::check();
        // Process form
        $this->redirect('/my-feature');
    }
}
```

### 3. Create a View
**File**: `views/my-feature/index.php`
```php
<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="main-content">
    <h1>My Feature</h1>
    <!-- Content here -->
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
```

## Common Tasks

### Access Session Data
```php
$username = Session::getUsername();
$domain = Session::getDomain();
$isSuperuser = Session::isSuperuser();
$role = Session::getUserRole();
```

### Call cPanel API
```php
$cpanel = new CpanelService();
$emails = $cpanel->getEmails();
$domains = $cpanel->getDomains();
$ssl = $cpanel->getSslCerts();
```

### Database Operations
```php
$userModel = new User();
$user = $userModel->findByDomain($domain);
$allUsers = $userModel->all();
$userModel->create($data);
$userModel->update($id, $data);
$userModel->delete($id);
```

### Flash Messages
```php
// Set message
Session::set('success', 'Operation successful');
Session::set('error', 'Operation failed');

// Display in view
if (Session::has('success')) {
    echo '<div class="alert alert-success">' . h(Session::get('success')) . '</div>';
    Session::remove('success');
}
```

### CSRF Protection
```php
// In form
<form method="POST">
    <?php echo CSRF::field(); ?>
    <!-- form fields -->
</form>

// In controller
CSRF::check();
```

### Redirect
```php
$this->redirect('/dashboard');
redirect('/dashboard'); // From anywhere
```

### JSON Response
```php
$this->json(['success' => true, 'data' => $data]);
```

### Require Authentication
```php
$this->requireAuth(); // Any logged-in user
$this->requireSuperuser(); // Superuser only
```

### Check Permissions
```php
$permissionModel = new Permission();
$canAccess = $permissionModel->canAccess('admin', $userRole);
```

## Service Classes

### CpanelService
```php
$cpanel = new CpanelService();
$cpanel->getDiskUsage();
$cpanel->getDomains();
$cpanel->getEmails($page, $perPage);
$cpanel->createEmail($email, $password, $quota);
$cpanel->changePassword($email, $password);
$cpanel->deleteEmail($email);
$cpanel->getSslCerts();
$cpanel->checkServerStatus();
```

### ZohoService
```php
$zoho = new ZohoService();
$invoices = $zoho->getInvoices($domain);
```

### TrelloService
```php
$trello = new TrelloService();
$boards = $trello->getBoards();
$board = $trello->getBoardByName('SUPPORT TEAM');
$lists = $trello->getLists($boardId);
$cards = $trello->getCards($listId);
$card = $trello->getCard($cardId);
$trello->moveCard($cardId, $listId);
$trello->markCardComplete($cardId);
```

### WeatherService
```php
$weather = new WeatherService();
$current = $weather->getCurrentWeather();
$icon = $weather->getWeatherIcon($weatherCode);
```

### PaynowService
```php
$paynow = new PaynowService();
$url = $paynow->generatePaymentUrl($invoiceId, $amount, $reference, $email);
```

## Helper Functions

```php
h($string)                    // HTML escape
redirect($url)                // Redirect and exit
view($path, $data)            // Load view
asset($path)                  // Generate asset URL
env($key, $default)           // Get environment variable
```

## Database Helper

```php
$db = Database::getInstance();
$conn = $db->getConnection();
$result = $db->query($sql);
$stmt = $db->prepare($sql);
$id = $db->lastInsertId();
```

## URL Structure

```
Old: index.php?page=emails&action=create
New: /emails/create

Old: index.php?page=admin&sub=users
New: /admin/users
```

## Asset Paths

```
Old: css/style.css
New: /public/css/style.css

Old: assets/images/logo.png
New: /public/assets/images/logo.png
```

## Debugging

### Enable Error Display
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Log to File
```php
error_log("Debug: " . print_r($data, true));
```

### Dump and Die
```php
echo '<pre>';
var_dump($data);
die();
```

## Testing Checklist

- [ ] Test without login (should redirect to login)
- [ ] Test with regular user (should work)
- [ ] Test with superuser (should have admin access)
- [ ] Test CSRF protection (remove token, should fail)
- [ ] Test validation (empty fields, invalid data)
- [ ] Test success messages
- [ ] Test error messages
- [ ] Test redirects
- [ ] Test mobile responsive
- [ ] Test browser compatibility

## Common Errors

### "Class not found"
- Check autoload.php includes the path
- Check class name matches filename
- Check namespace if using namespaces

### "CSRF token validation failed"
- Ensure CSRF::field() in form
- Ensure CSRF::check() in controller
- Check session is started

### "Call to undefined method"
- Check method exists in controller
- Check method is public
- Check spelling

### "View not found"
- Check view path is correct
- Check file exists in views/ folder
- Check file extension is .php

## Best Practices

1. **Always validate input** - Never trust user data
2. **Always escape output** - Use h() for HTML
3. **Always use CSRF** - On all POST requests
4. **Always check auth** - Use requireAuth() or requireSuperuser()
5. **Keep controllers thin** - Move logic to services/models
6. **Keep views dumb** - Minimal PHP in views
7. **Use services for APIs** - Don't call APIs directly from controllers
8. **Use models for database** - Don't write SQL in controllers
9. **Use flash messages** - For success/error feedback
10. **Follow naming conventions** - PascalCase for classes, camelCase for methods

## File Naming Conventions

- **Controllers**: `PascalCase` + `Controller.php` (e.g., `EmailController.php`)
- **Models**: `PascalCase.php` (e.g., `User.php`)
- **Services**: `PascalCase` + `Service.php` (e.g., `CpanelService.php`)
- **Views**: `lowercase.php` or `kebab-case.php` (e.g., `index.php`, `create-email.php`)
- **Routes**: Use kebab-case (e.g., `/email-accounts`, `/change-password`)

## Git Workflow

```bash
# Create feature branch
git checkout -b feature/my-feature

# Make changes and commit
git add .
git commit -m "Add my feature"

# Push to remote
git push origin feature/my-feature

# Create pull request
# After review, merge to main
```

## Deployment

1. Upload files to server
2. Run `composer install`
3. Update `.env` with production values
4. Set proper file permissions
5. Test all features
6. Monitor error logs

---

**Need Help?** Check the documentation:
- `MVC_STRUCTURE.md` - Complete structure overview
- `MVC_MIGRATION_GUIDE.md` - Migration steps
- `PHASE2_COMPLETE.md` - Controller reference
