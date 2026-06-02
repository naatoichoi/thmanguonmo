<?php
class SessionHelper {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn() {
        self::start();
        return isset($_SESSION['user_id']); 
    }

    public static function requireLogin() {
        self::start();
        if (!self::isLoggedIn()) {
            header('Location: /Account/login');
            exit;
        }
    }

    public static function isAdmin() {
        self::start();
        return self::hasRole('admin');
    }

    public static function getRole() {
        self::start();
        return $_SESSION['role'] ?? 'guest';
    }

    public static function hasRole($role) {
        self::start();
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    public static function getUserName() {
        self::start();
        return $_SESSION['username'] ?? 'Khách';
    }

    public static function logout() {
        self::start();
        session_destroy();
    }
}
?>