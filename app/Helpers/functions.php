<?php

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function view($viewPath, $data = []) {
    extract($data);
    $viewFile = __DIR__ . "/../../views/{$viewPath}.php";
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        throw new Exception("View not found: {$viewPath}");
    }
}

function asset($path) {
    return "/public/{$path}";
}

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}
