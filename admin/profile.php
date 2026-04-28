<?php
// ============================================================
//  admin/profile.php
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';
requireRole('admin');
require_once __DIR__ . '/../config/db.php';

// Always fetch fresh from DB
$stmt = $pdo->prepare('SELECT id,name,email,role,created_at FROM users WHERE id=? LIMIT 1');
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch();

if (!$profile) {
    session_destroy();
    header('Location: ' . rootUrl . 'auth/login.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (empty($name))                                              $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (!empty($password) && strlen($password) < 6)                $errors[] = 'Password must be ≥6 chars.';
    if (!empty($password) && $password !== $confirm)               $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email=? AND id!=?');
        $check->execute([$email, $profile['id']]);
        if ($check->fetch()) $errors[] = 'This email is in use by another account.';
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare('UPDATE users SET name=?,email=?,password=? WHERE id=?')->execute([$name,$email,$hashed,$profile['id']]);
        } else {
            $pdo->prepare('UPDATE users SET name=?,email=? WHERE id=?')->execute([$name,$email,$profile['id']]);
        }
        $_SESSION['name']  = $name;
        $_SESSION['email'] = $email;
        $profile['name']   = $name;
        $profile['email']  = $email;
        $success = true;
    }
}

$activePage = 'profile';
$root = rootUrl;
$initials = strtoupper(substr($profile['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile – Admin · CorpPortal</title>
<link rel="stylesheet" href="<?= $root ?>assets/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require_once __DIR__ . '/../includes/nav_admin.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <button class="topbar-menu-btn" id="menuBtn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div>
          <div class="breadcrumb">
            <a href="<?= $root ?>admin/dashboard.php">Dashboard</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="current">My Profile</span>
          </div>
          <div class="page-title">My Profile</div>
        </div>
      </div>
      <div class="topbar-right">
        <div class="topbar-avatar"><?= $initials ?></div>
      </div>
    </div>

    <div class="page-body">

      <?php if ($success): ?>
        <div class="alert alert-success">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          Profile updated successfully!
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="profile-grid">

        <!-- Left Card -->
        <div class="profile-card-left">
          <div class="profile-banner"></div>
          <div class="profile-avatar-wrap">
            <div class="profile-avatar"><?= $initials ?></div>
          </div>
          <div class="profile-card-body">
            <div class="profile-name"><?= e($profile['name']) ?></div>
            <div class="profile-role-label">Administrator</div>
            <hr class="profile-divider">
            <div class="profile-meta">
              <div class="profile-meta-item">
                <div class="meta-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                  <span class="meta-label">Email</span>
                  <span class="meta-value"><?= e($profile['email']) ?></span>
                </div>
              </div>
              <div class="profile-meta-item">
                <div class="meta-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                  <span class="meta-label">Role</span>
                  <span class="meta-value"><span class="badge badge-admin">Admin</span></span>
                </div>
              </div>
              <div class="profile-meta-item">
                <div class="meta-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                  <span class="meta-label">Member Since</span>
                  <span class="meta-value"><?= date('F j, Y', strtotime($profile['created_at'])) ?></span>
                </div>
              </div>
              <div class="profile-meta-item">
                <div class="meta-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                  <span class="meta-label">User ID</span>
                  <span class="meta-value">#<?= $profile['id'] ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Info + Edit -->
        <div>
          <div class="profile-info-card">
            <div class="info-section">
              <div class="info-section-title">Account Information</div>
              <div class="info-grid">
                <div class="info-field">
                  <div class="info-field-label">Full Name</div>
                  <div class="info-field-value"><?= e($profile['name']) ?></div>
                </div>
                <div class="info-field">
                  <div class="info-field-label">Email Address</div>
                  <div class="info-field-value"><?= e($profile['email']) ?></div>
                </div>
                <div class="info-field">
                  <div class="info-field-label">Role</div>
                  <div class="info-field-value"><span class="badge badge-admin">Administrator</span></div>
                </div>
                <div class="info-field">
                  <div class="info-field-label">Account Created</div>
                  <div class="info-field-value"><?= date('M j, Y · g:i A', strtotime($profile['created_at'])) ?></div>
                </div>
              </div>
            </div>

            <div class="info-section">
              <div class="info-section-title">Update Profile</div>
              <form method="POST" action="" data-loading>
                <div class="info-grid" style="margin-bottom:16px">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= e($profile['name']) ?>" required>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($profile['email']) ?>" required>
                  </div>
                </div>
                <div class="info-grid" style="margin-bottom:20px">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">New Password <span style="color:var(--slate-400);font-weight:400;text-transform:none">(optional)</span></label>
                    <div class="input-group">
                      <input type="password" name="password" class="form-control" placeholder="Leave blank to keep">
                      <button type="button" class="input-group-btn toggle-password">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      </button>
                    </div>
                  </div>
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm" class="form-control" placeholder="Repeat new password">
                  </div>
                </div>
                <button type="submit" class="btn btn-primary" data-submit-btn>
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                  Save Changes
                  <div class="spinner"></div>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="<?= $root ?>assets/js/main.js"></script>
</body>
</html>
