-- MySQL script to create a new user in Nimbus Dashboard
-- Replace placeholders with actual values from Make.com

INSERT INTO users (
    cpanel_username,
    domain,
    api_token,
    full_name,
    profile_picture_url,
    is_superuser,
    package
) VALUES (
    '{{cpanel_username}}',
    '{{domain}}',
    '{{api_token}}',
    '{{full_name}}',
    '{{profile_picture_url}}',
    {{is_superuser}},
    '{{package}}'
);

-- Example with sample data:
-- INSERT INTO users (cpanel_username, domain, api_token, full_name, profile_picture_url, is_superuser, package)
-- VALUES ('johndoe', 'example.com', 'ABC123TOKEN456', 'John Doe', 'https://example.com/avatar.jpg', 0, 'Soloprenuer');

-- Placeholders for Make.com:
-- {{cpanel_username}} - cPanel username (e.g., 'johndoe')
-- {{domain}} - Domain name (e.g., 'example.com')
-- {{api_token}} - cPanel API token (generated from cPanel)
-- {{full_name}} - User's full name (e.g., 'John Doe')
-- {{profile_picture_url}} - Profile picture URL (optional, can be NULL or empty string)
-- {{is_superuser}} - 0 for regular user, 1 for superuser (no quotes, integer)
-- {{package}} - Package name: 'Soloprenuer', 'Small Business', or 'Enterprise'
