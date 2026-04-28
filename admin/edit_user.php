<?php
// ============================================================
//  admin/edit_user.php
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';
requireRole('admin');
require_once __DIR__ . '/../config/db.php';

$userId = (int)($_GET['id'] ?? 0);

if (!$userId) {
    setFlash('error', 'Invalid user.');
    header('Location: users.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id,name,email,role,created_at FROM users WHERE id=? LIMIT 1');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    setFlash('error', 'User not found.');
    header('Location: users.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $role     = trim($_POST['role']     ?? 'user');
    $password = trim($_POST['password'] ?? '');

    if (empty($name))                                              $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (!in_array($role, ['admin','user']))                         $errors[] = 'Invalid role.';
    if (!empty($password) && strlen($password) < 6)                $errors[] = 'New password must be ≥6 characters.';

    // Email uniqueness (excluding self)
    if (empty($errors)) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email=? AND id != ?');
        $check->execute([$email, $userId]);
        if ($check->fetch()) $errors[] = 'Email is already in use by another account.';
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare('UPDATE users SET name=?,email=?,role=?,password=? WHERE id=?')
                ->execute([$name, $email, $role, $hashed, $userId]);
        } else {
            $pdo->prepare('UPDATE users SET name=?,email=?,role=? WHERE id=?')
                ->execute([$name, $email, $role, $userId]);
        }

        // If admin edited themselves, refresh session name
        if ($userId === (int)$_SESSION['user_id']) {
            $_SESSION['name']  = $name;
            $_SESSION['email'] = $email;
        }

        setFlash('success', "User '{$name}' updated successfully.");
        header('Location: users.php');
        exit;
    }

    // Repopulate form with POST data on error
    $user['name']  = $name;
    $user['email'] = $email;
    $user['role']  = $role;
}

$activePage = 'users';
$root = rootUrl;
$initials = strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User – Admin · CorpPortal</title>
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
            <a href="<?= $root ?>admin/users.php">Users</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="current">Edit</span>
          </div>
          <div class="page-title">Edit User</div>
        </div>
      </div>
      <div class="topbar-right">
        <a href="users.php" class="btn btn-outline btn-sm">← Back to Users</a>
        <div class="topbar-avatar" onclick="window.location='<?= $root ?>admin/profile.php'"><?= $initials ?></div>
      </div>
    </div>

    <div class="page-body">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="card" style="max-width:580px">
        <div class="card-header">
          <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal-600)" stroke-width="2" style="margin-right:2px"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Member · <span style="color:var(--slate-400);font-weight:400"><?= e($user['name']) ?></span>
          </div>
        </div>
        <div class="card-body">
          <form method="POST" action="" data-loading>
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" value="<?= e($user['name']) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Role</label>
              <select name="role" class="form-control">
                <option value="user"  <?= $user['role'] === 'user'  ? 'selected' : '' ?>>Employee / User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">New Password <span style="font-weight:400;text-transform:none;color:var(--slate-400)">(leave blank to keep current)</span></label>
              <div class="input-group">
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters">
                <button type="button" class="input-group-btn toggle-password">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:4px">
              <button type="submit" class="btn btn-primary" data-submit-btn>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Changes
                <div class="spinner"></div>
              </button>
              <a href="users.php" class="btn btn-outline">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>
<script src="<?= $root ?>assets/js/main.js"></script>
</body>
</html>
