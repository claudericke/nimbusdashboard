<?php

class CSRF
{
    public static function ensureToken()
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
    }

    public static function field()
    {
        self::ensureToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(Session::get('csrf_token')) . '">';
    }

    public static function getToken()
    {
        self::ensureToken();
        return Session::get('csrf_token');
    }

    public static function check()
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals(Session::get('csrf_token', ''), $token)) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
}
