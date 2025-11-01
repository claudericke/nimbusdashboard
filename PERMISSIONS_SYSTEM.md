# User Roles & Permissions System

## Overview
Multilevel user management system with role-based access control for menu items.

## Database Changes

### Run Migration
Execute `migrations/add_user_roles_permissions.sql` to add:
- `user_role` column to `users` table
- `roles` table for role definitions
- `permissions` table for menu access control

## User Roles

### 1. Superuser
- Full system access
- Can access Admin panel
- Can manage users and permissions
- Can access Tickets system

### 2. Administrator
- Full dashboard access
- Can manage domains, emails, SSL, billing
- Can access Tickets system
- Cannot access Admin panel

### 3. Client (Default)
- Standard customer access
- Can manage domains, emails, SSL, billing
- Cannot access Tickets or Admin

### 4. Viewer
- Read-only access
- Can view dashboard, domains, SSL, billing
- Cannot manage emails or settings
- Cannot access Tickets or Admin

## Login Changes

### Old System
- Required: Domain, cPanel Username, Password

### New System
- Required: Domain, Password
- Username is fetched from database by domain

## Permission Management

### Check Permission in Code
```php
if (canAccess('emails', $userRole)) {
    // Show emails menu
}
```

### Menu Items
- dashboard
- domains
- emails
- ssl
- billing
- settings
- tickets
- admin

## Modifying Permissions

### Via Database
```sql
-- Grant email access to viewer role
UPDATE permissions SET can_access = 1 
WHERE role_name = 'viewer' AND menu_item = 'emails';

-- Revoke tickets access from client role
UPDATE permissions SET can_access = 0 
WHERE role_name = 'client' AND menu_item = 'tickets';
```

### Add New Role
```sql
-- Create new role
INSERT INTO roles (role_name, role_label) VALUES ('manager', 'Manager');

-- Set permissions for new role
INSERT INTO permissions (role_name, menu_item, can_access) VALUES
('manager', 'dashboard', 1),
('manager', 'domains', 1),
('manager', 'emails', 1),
('manager', 'ssl', 1),
('manager', 'billing', 0),
('manager', 'settings', 1),
('manager', 'tickets', 1),
('manager', 'admin', 0);
```

## Assigning Roles to Users

```sql
-- Set user role
UPDATE users SET user_role = 'admin' WHERE cpanel_domain = 'example.com';

-- Set user role to viewer
UPDATE users SET user_role = 'viewer' WHERE cpanel_domain = 'client.com';
```

## Session Variables
- `$_SESSION['user_role']` - Current user's role
- `$_SESSION['is_superuser']` - Legacy superuser flag (still used)
- `$userPermissions` - Array of menu permissions loaded on login
