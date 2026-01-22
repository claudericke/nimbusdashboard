<?php

// Global Fatal Error Handler
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (ob_get_length()) {
            ob_end_clean(); // Clear partial output
        }

        // Pass error message to view if valid
        $errorMessage = $error['message'] . " in " . $error['file'] . ":" . $error['line'];

        // Render friendly error page
        if (file_exists(__DIR__ . '/views/errors/fatal.php')) {
            require __DIR__ . '/views/errors/fatal.php';
        } else {
            echo "<h1>Critical System Error</h1><p>Please contact support.</p>";
        }
        exit;
    }
});

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
