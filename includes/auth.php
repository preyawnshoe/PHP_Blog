<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin_logged_in(): bool {
    return isset($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin_logged_in()) {
        $redirect = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        header('Location: login.php?redirect=' . urlencode($redirect));
        exit;
    }
}

function login_admin(int $adminId, string $username): void {
    $_SESSION['admin_id'] = $adminId;
    $_SESSION['admin_username'] = $username;
}

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