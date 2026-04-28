<?php
// ============================================================
//  auth/signup.php
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';
require_once __DIR__ . '/../config/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . rootUrl . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');
    $role     = trim($_POST['role']     ?? 'user');

    // Validation
    if (empty($name))                                   $errors[] = 'Full name is required.';
    elseif (strlen($name) < 2)                          $errors[] = 'Name must be at least 2 characters.';

    if (empty($email))                                  $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';

    if (empty($password))                               $errors[] = 'Password is required.';
    elseif (strlen($password) < 6)                      $errors[] = 'Password must be at least 6 characters.';

    if ($password !== $confirm)                         $errors[] = 'Passwords do not match.';

    if (!in_array($role, ['admin', 'user']))             $errors[] = 'Invalid role selected.';

    // Check duplicate email
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'This email is already registered.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt   = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hashed, $role]);

        setFlash('success', 'Account created successfully! Please sign in.');
        header('Location: login.php');
        exit;
    }
}

$root = rootUrl;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account – CorpPortal</title>
<link rel="stylesheet" href="../assets/css/style.css">
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
      <h1 class="auth-brand-title">Join CorpPortal</h1>
      <p class="auth-brand-sub">Create your account to get access to your team dashboard, profile, and tools.</p>

      <div class="auth-features">
        <div class="auth-feature-item">
          <div class="auth-feature-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          </div>
          Quick setup in under a minute
        </div>
        <div class="auth-feature-item">
          <div class="auth-feature-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          Secure, password-protected access
        </div>
        <div class="auth-feature-item">
          <div class="auth-feature-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          Choose Admin or Employee role
        </div>
      </div>
    </div>
  </div>

  <!-- Form Panel -->
  <div class="auth-form-panel">
    <div class="auth-form-box">
      <div class="auth-form-header">
        <h1>Create your account</h1>
        <p>Fill in the details below to get started</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div><?php foreach ($errors as $err): ?><div><?= htmlspecialchars($err) ?></div><?php endforeach; ?></div>
        </div>
      <?php endif; ?>

      <form method="POST" action="" data-loading>
        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input type="text" id="name" name="name" class="form-control"
                 placeholder="John Smith"
                 value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                 required>
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control"
                 placeholder="john@company.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 required>
        </div>

        <div class="form-group">
          <label class="form-label" for="role">Role</label>
          <select id="role" name="role" class="form-control" required>
            <option value="user"  <?= ($_POST['role'] ?? 'user') === 'user'  ? 'selected' : '' ?>>Employee / Intern</option>
            <option value="admin" <?= ($_POST['role'] ?? '')      === 'admin' ? 'selected' : '' ?>>Admin / Manager</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-group">
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Min. 6 characters" required>
            <button type="button" class="input-group-btn toggle-password" aria-label="Toggle password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="confirm">Confirm Password</label>
          <input type="password" id="confirm" name="confirm" class="form-control"
                 placeholder="Repeat password" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block" data-submit-btn style="margin-top:8px">
          <span>Create Account</span>
          <div class="spinner"></div>
        </button>
      </form>

      <div class="auth-footer-link" style="margin-top:20px">
        Already have an account? <a href="<?= $root ?>auth/login.php">Sign in</a>
      </div>
    </div>
  </div>

</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
