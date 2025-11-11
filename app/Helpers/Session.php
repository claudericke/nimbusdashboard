<?php

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    public static function destroy() {
        session_destroy();
    }

    public static function isLoggedIn() {
        return self::has('cpanel_username') && self::has('cpanel_domain');
    }

    public static function isSuperuser() {
        return self::get('is_superuser', 0) == 1;
    }

    public static function getUserRole() {
        return self::get('user_role', 'client');
    }

    public static function getUsername() {
        return self::get('cpanel_username');
    }

    public static function getDomain() {
        return self::get('cpanel_domain');
    }

    public static function getApiToken() {
        return self::get('cpanel_api_token');
    }
}
