<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('is_admin_logged_in')) {
    function is_admin_logged_in(): bool {
        return isset($_SESSION['admin_id']);
    }
}

if (!function_exists('require_admin')) {
    function require_admin(): void {
        if (!is_admin_logged_in()) {
            $redirect = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
            header('Location: login.php?redirect=' . urlencode($redirect));
            exit;
        }
    }
}

if (!function_exists('login_admin')) {
    function login_admin(int $adminId, string $username): void {
        $_SESSION['admin_id'] = $adminId;
        $_SESSION['admin_username'] = $username;
    }
}

if (!function_exists('logout_admin')) {
    function logout_admin(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }
} 