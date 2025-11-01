-- Add user_role column to users table
ALTER TABLE users ADD COLUMN user_role VARCHAR(50) DEFAULT 'client' AFTER is_superuser;

-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    role_label VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create permissions table
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL,
    menu_item VARCHAR(50) NOT NULL,
    can_access TINYINT(1) DEFAULT 1,
    FOREIGN KEY (role_name) REFERENCES roles(role_name) ON DELETE CASCADE,
    UNIQUE KEY unique_role_menu (role_name, menu_item)
);

-- Insert default roles
INSERT INTO roles (role_name, role_label) VALUES
('superuser', 'Super User'),
('admin', 'Administrator'),
('client', 'Client'),
('viewer', 'Viewer');

-- Insert default permissions for superuser (all access)
INSERT INTO permissions (role_name, menu_item, can_access) VALUES
('superuser', 'dashboard', 1),
('superuser', 'domains', 1),
('superuser', 'emails', 1),
('superuser', 'ssl', 1),
('superuser', 'billing', 1),
('superuser', 'settings', 1),
('superuser', 'tickets', 1),
('superuser', 'admin', 1);

-- Insert default permissions for admin
INSERT INTO permissions (role_name, menu_item, can_access) VALUES
('admin', 'dashboard', 1),
('admin', 'domains', 1),
('admin', 'emails', 1),
('admin', 'ssl', 1),
('admin', 'billing', 1),
('admin', 'settings', 1),
('admin', 'tickets', 1),
('admin', 'admin', 0);

-- Insert default permissions for client
INSERT INTO permissions (role_name, menu_item, can_access) VALUES
('client', 'dashboard', 1),
('client', 'domains', 1),
('client', 'emails', 1),
('client', 'ssl', 1),
('client', 'billing', 1),
('client', 'settings', 1),
('client', 'tickets', 0),
('client', 'admin', 0);

-- Insert default permissions for viewer (read-only)
INSERT INTO permissions (role_name, menu_item, can_access) VALUES
('viewer', 'dashboard', 1),
('viewer', 'domains', 1),
('viewer', 'emails', 0),
('viewer', 'ssl', 1),
('viewer', 'billing', 1),
('viewer', 'settings', 0),
('viewer', 'tickets', 0),
('viewer', 'admin', 0);

-- Update existing superuser accounts
UPDATE users SET user_role = 'superuser' WHERE is_superuser = 1;

-- Update existing non-superuser accounts to client
UPDATE users SET user_role = 'client' WHERE is_superuser = 0 OR is_superuser IS NULL;
