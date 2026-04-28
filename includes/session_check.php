<?php
// ============================================================
//  includes/session_check.php
//  Include at the top of EVERY protected page.
//  Usage:
//    require_once __DIR__ . '/../includes/session_check.php';
//    requireRole('admin');   // or requireRole('user')
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect to login if not authenticated.
 * Optionally enforce a specific role.
 *
 * @param string|null $role  'admin' | 'user' | null (any authenticated user)
 */
function requireRole(?string $role = null): void
{
    // Must be logged in
    if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
        header('Location: ' . rootUrl . 'auth/login.php');
        exit;
    }

    // Role check
    if ($role !== null && $_SESSION['role'] !== $role) {
        // Wrong role → send to their own dashboard
        if ($_SESSION['role'] === 'admin') {
            header('Location: ' . rootUrl . 'admin/dashboard.php');
        } else {
            header('Location: ' . rootUrl . 'user/dashboard.php');
        }
        exit;
    }
}

/**
 * Build an absolute-ish root URL that works under any sub-folder.
 */
// function rootUrl: string
// {
//     // Detect base path from SCRIPT_NAME
//     $script = $_SERVER['SCRIPT_NAME'] ?? '/';
//     // Walk up until we hit the project root (index.php lives there)
//     // Simple approach: resolve relative to DOCUMENT_ROOT
//     $base = rtrim(dirname(dirname(__FILE__)), '/\\') . '/';
//     // For URLs we need the HTTP base
//     $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
//     $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
//     // Strip filesystem root
//     $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
//     $projectRoot = rtrim(dirname(dirname(__FILE__)), '/\\');
//     $urlPath = str_replace($docRoot, '', $projectRoot);
//     $urlPath = str_replace('\\', '/', $urlPath);
//     return $proto . '://' . $host . $urlPath . '/';
// }

/**
 * Escape output safely.
 */
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Flash message helpers.
 */
function setFlash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
