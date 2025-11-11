<?php

// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load autoloader
require_once __DIR__ . '/autoload.php';

// Load helper functions
require_once __DIR__ . '/app/Helpers/functions.php';

// Start session
Session::start();

// Initialize CSRF protection
CSRF::ensureToken();

// Create router
$router = new Router();

// Load routes
require __DIR__ . '/routes/web.php';

// Dispatch request
$router->dispatch($_SERVER['REQUEST_URI']);
