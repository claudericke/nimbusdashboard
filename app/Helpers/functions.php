<?php

function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function view($viewPath, $data = [])
{
    extract($data);
    $viewFile = __DIR__ . "/../../views/{$viewPath}.php";
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        throw new Exception("View not found: {$viewPath}");
    }
}

function asset($path)
{
    return "/public/{$path}";
}

function env($key, $default = null)
{
    return $_ENV[$key] ?? $default;
}

function formatTicketName($name)
{
    if (strpos($name, '{') !== false) {
        $parts = explode('-', $name, 2);
        $prefix = trim($parts[0]);
        $jsonStr = $parts[1] ?? '';
        $json = @json_decode($jsonStr, true);
        if ($json) {
            return $prefix . ": " . ($json['value'] ?? $json['title'] ?? 'New Request');
        }
    }
    return $name;
}
