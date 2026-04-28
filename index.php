<?php
// ============================================================
//  index.php – Root entry point / router
//  http://localhost/Projects/role-portal/
// ============================================================

require_once __DIR__ . '/includes/session_check.php';

// If already logged in, redirect to appropriate dashboard
if (!empty($_SESSION['user_id']) && !empty($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit;
}

// Not logged in → go to login
header('Location: auth/login.php');
exit;
