<?php
// ============================================================
//  auth/logout.php
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';

// Destroy all session data
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

// Redirect to login with flash
session_start();
setFlash('success', 'You have been signed out successfully.');
header('Location: login.php');
exit;
