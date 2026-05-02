<?php
// ============================================================
//  auth/login.php
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

// Already logged in?
if (!empty($_SESSION['user_id'])) {
    header('Location: ' . rootUrl . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email))    $errors[] = 'Email is required.';
    if (empty($password)) $errors[] = 'Password is required.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session to prevent fixation
            session_regenerate_id(true);

            // ---- Role is ALWAYS taken from DB ----
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];   // single source of truth
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];

            if ($user['role'] === 'admin') {
                header('Location: ' . rootUrl . 'admin/dashboard.php');
            } else {
                header('Location: ' . rootUrl . 'user/dashboard.php');
            }
            exit;
        } else {
            $errors[] = 'Invalid email or password. Please try again.';
        }
    }
}

$root = rootUrl;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In – CorpPortal</title>
<link rel="stylesheet" href="<?= $root ?>/assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">

  <!-- Brand Panel -->
  <div class="auth-brand-panel">
    <div class="auth-brand-content">
      <div class="auth-logo">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
          <polygon points="12 2 2 7 12 12 22 7 12 2"/>
          <polyline points="2 17 12 22 22 17"/>
          <polyline points="2 12 12 17 22 12"/>
        </svg>
      </div>
      <h1 class="auth-brand-title">CorpPortal</h1>
      <p class="auth-brand-sub">Your secure, role-based company workspace. Sign in to access your dashboard.</p>

      <div class="auth-features">
        <div class="auth-feature-item">
          <div class="auth-feature-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
          Role-based secure access control
        </div>
        <div class="auth-feature-item">
          <div class="auth-feature-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          Admin & Employee dashboards
        </div>
        <div class="auth-feature-item">
          <div class="auth-feature-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          Real-time user management
        </div>
      </div>
    </div>
  </div>

  <!-- Form Panel -->
  <div class="auth-form-panel">
    <div class="auth-form-box">
      <div class="auth-form-header">
        <h1>Welcome back 👋</h1>
        <p>Sign in to your CorpPortal account</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div>
            <?php foreach ($errors as $err): ?>
              <div><?= htmlspecialchars($err) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>">
          <?= htmlspecialchars($flash['msg']) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" data-loading>
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control <?= !empty($errors) ? 'is-invalid' : '' ?>"
                 placeholder="you@company.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 required autocomplete="email">
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-group">
            <input type="password" id="password" name="password"
                   class="form-control <?= !empty($errors) ? 'is-invalid' : '' ?>"
                   placeholder="Enter your password"
                   required autocomplete="current-password">
            <button type="button" class="input-group-btn toggle-password" aria-label="Toggle password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block" data-submit-btn style="margin-top:8px">
          <span>Sign In</span>
          <div class="spinner"></div>
        </button>
      </form>

      <div class="divider-text">or</div>

      <div class="auth-footer-link">
        Don't have an account? <a href="<?= $root ?>auth/signup.php">Create one</a>
      </div>

      <!-- Demo hint -->
      <div style="margin-top:24px;padding:14px 16px;background:var(--teal-50);border-radius:8px;border:1px solid var(--teal-100);">
        <p style="font-size:.78rem;font-weight:700;color:var(--teal-700);margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;">Demo Credentials</p>
        <p style="font-size:.8rem;color:var(--teal-800);">
          <strong>Admin:</strong> manager@company.com<br>
          <strong>User:</strong> user@company.com<br>
          <strong>Password (both):</strong> PASSWORD
        </p>
      </div>
    </div>
  </div>

</div>

<script src="<?= $root ?>assets/js/main.js"></script>
</body>
</html>
