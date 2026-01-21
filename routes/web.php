<?php

// Authentication routes
$router->get('/', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/switch-domain', 'AuthController@switchDomain');

// Dashboard
$router->get('/dashboard', 'DashboardController@index');

// Domains
$router->get('/domains', 'DomainController@index');

// Emails
$router->get('/emails', 'EmailController@index');
$router->get('/emails/create', 'EmailController@create');
$router->post('/emails/store', 'EmailController@store');
$router->post('/emails/change-password', 'EmailController@changePassword');
$router->post('/emails/delete', 'EmailController@delete');

// SSL
$router->get('/ssl', 'SslController@index');

// Billing
$router->get('/billing', 'BillingController@index');

// Settings
$router->get('/settings', 'SettingsController@index');
$router->post('/settings/update', 'SettingsController@update');
$router->post('/settings/upload-avatar', 'SettingsController@uploadAvatar');

// Tickets (Superuser only)
$router->get('/tickets/new', 'TicketController@newTickets');
$router->get('/tickets/open', 'TicketController@openTickets');
$router->get('/tickets/awaiting', 'TicketController@awaitingTickets');
$router->get('/tickets/closed', 'TicketController@closedTickets');
$router->post('/tickets/close', 'TicketController@closeTicket');
$router->get('/tickets/check-new', 'TicketController@checkNew');
$router->get('/tickets/card/{id}', 'TicketController@getCard');

// Admin (Superuser only)
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/users/create', 'AdminController@createUser');
$router->post('/admin/users/edit', 'AdminController@editUser');
$router->post('/admin/users/delete', 'AdminController@deleteUser');

$router->get('/admin/quotes', 'AdminController@quotes');
$router->post('/admin/quotes/create', 'AdminController@createQuote');
$router->post('/admin/quotes/edit', 'AdminController@editQuote');
$router->post('/admin/quotes/delete', 'AdminController@deleteQuote');

$router->get('/admin/permissions', 'AdminController@permissions');
$router->post('/admin/permissions/update', 'AdminController@updatePermissions');

// Notifications
$router->get('/notifications/latest', 'NotificationController@latest');
$router->post('/notifications/read-all', 'NotificationController@markRead');
