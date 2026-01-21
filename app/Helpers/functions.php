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
    // Try to find JSON block starting with {
    $bracePos = strpos($name, '{');
    if ($bracePos !== false) {
        $prefixPart = substr($name, 0, $bracePos);
        $jsonPart = substr($name, $bracePos);

        // Clean up prefix (remove trailing dashes and spaces)
        $prefix = preg_replace('/[\s\-\–\—]+$/u', '', $prefixPart);
        $json = @json_decode($jsonPart, true);

        if ($json) {
            $ticketTitle = $json['value'] ?? $json['title'] ?? 'New Request';
            return trim($prefix) . ": " . trim($ticketTitle);
        }
    }
    return $name;
}
