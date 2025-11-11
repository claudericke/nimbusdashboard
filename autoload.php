<?php

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/Controllers/' . $class . '.php',
        __DIR__ . '/app/Models/' . $class . '.php',
        __DIR__ . '/app/Services/' . $class . '.php',
        __DIR__ . '/app/Helpers/' . $class . '.php',
        __DIR__ . '/app/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
