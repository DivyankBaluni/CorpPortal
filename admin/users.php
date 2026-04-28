<?php
// ============================================================
//  admin/users.php – Full CRUD User Management
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';
requireRole('admin');
require_once __DIR__ . '/../config/db.php';

// ── ADD USER ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role']     ?? 'user');
    $errors   = [];

    if (empty($name))                                   $errors[] = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (strlen($password) < 6)                          $errors[] = 'Password must be ≥6 chars.';
    if (!in_array($role, ['admin','user']))              $errors[] = 'Invalid role.';

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'Email already exists.';
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)')->execute([$name,$email,$hashed,$role]);
        setFlash('success', "User '{$name}' added successfully.");
        header('Location: users.php');
        exit;
    } else {
        $addErrors  = $errors;
        $addFormData = $_POST;
    }
}

// ── DELETE USER ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $delId = (int)($_POST['user_id'] ?? 0);
    if ($delId && $delId !== (int)$_SESSION['user_id']) {
        $stmt = $pdo->prepare('SELECT name FROM users WHERE id=?');
        $stmt->execute([$delId]);
        $delUser = $stmt->fetch();
        if ($delUser) {
            $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$delId]);
            setFlash('success', "User '{$delUser['name']}' deleted.");
        }
    } else {
        setFlash('error', 'Cannot delete yourself.');
    }
    header('Location: users.php');
    exit;
}

// ── FETCH USERS ─────────────────────────────────────────────
$users = $pdo->query('SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC')->fetchAll();

$activePage = 'users';
$root = rootUrl;
$initials = strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management – Admin · CorpPortal</title>
<link rel="stylesheet" href="<?= $root ?>assets/css/style.css">
</head>
<body>
<div class="app-layout">

  <?php require_once __DIR__ . '/../includes/nav_admin.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <button class="topbar-menu-btn" id="menuBtn" aria-label="Open menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div>
          <div class="breadcrumb">
            <a href="<?= $root ?>admin/dashboard.php">Dashboard</a>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="current">User Management</span>
          </div>
          <div class="page-title">User Management</div>
        </div>
      </div>
      <div class="topbar-right">
        <button class="btn btn-primary btn-sm" onclick="openModal('addUserModal')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add User
        </button>
        <div class="topbar-avatar" onclick="window.location='<?= $root ?>admin/profile.php'"><?= $initials ?></div>
      </div>
    </div>

    <div class="page-body">

      <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div>
      <?php endif; ?>

      <?php if (!empty($addErrors)): ?>
        <div class="alert alert-error">
          <?php foreach ($addErrors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal-600)" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            All Members <span style="color:var(--slate-400);font-weight:400;font-size:.85rem">(<?= count($users) ?>)</span>
          </div>
          <div class="search-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="userSearch" placeholder="Search by name, email, role…">
          </div>
        </div>
        <div class="card-body" style="padding-top:16px">
          <div class="table-wrapper">
            <table class="table" id="usersTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Member</th>
                  <th>Role</th>
                  <th>Joined</th>
                  <th style="text-align:right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($users)): ?>
                  <tr><td colspan="5">
                    <div class="empty-state">
                      <div class="empty-state-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                      </div>
                      <h3>No users yet</h3>
                      <p>Add your first user to get started.</p>
                    </div>
                  </td></tr>
                <?php else: ?>
                  <?php foreach ($users as $u): ?>
                    <tr>
                      <td style="color:var(--slate-400);font-size:.8rem">#<?= $u['id'] ?></td>
                      <td>
                        <div class="user-cell">
                          <div class="user-cell-avatar <?= $u['role'] === 'admin' ? 'is-admin' : '' ?>">
                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                          </div>
                          <div>
                            <div class="user-cell-name"><?= e($u['name']) ?></div>
                            <div class="user-cell-email"><?= e($u['email']) ?></div>
                          </div>
                        </div>
                      </td>
                      <td><span class="badge badge-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
                      <td style="color:var(--slate-500);font-size:.85rem"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                      <td>
                        <div class="action-group" style="justify-content:flex-end">
                          <a href="<?= $root ?>admin/edit_user.php?id=<?= $u['id'] ?>" class="btn btn-outline btn-sm btn-icon" title="Edit user">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                          </a>
                          <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                          <form id="del-<?= $u['id'] ?>" method="POST" action="" style="display:none">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                          </form>
                          <button class="btn btn-danger btn-sm btn-icon" data-delete-form="del-<?= $u['id'] ?>" title="Delete user">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                          </button>
                          <?php else: ?>
                            <span style="font-size:.72rem;color:var(--slate-400);padding:4px 8px">(you)</span>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- ─── Add User Modal ─────────────────────────────────── -->
<div class="modal-overlay" id="addUserModal" <?= !empty($addErrors) ? 'style="opacity:1;pointer-events:all"' : '' ?>>
  <div class="modal" <?= !empty($addErrors) ? 'style="transform:scale(1) translateY(0)"' : '' ?>>
    <div class="modal-header">
      <div class="modal-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal-600)" stroke-width="2" style="margin-right:6px;vertical-align:middle"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        Add New User
      </div>
      <button class="modal-close" onclick="closeModal('addUserModal')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form method="POST" action="" data-loading>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Jane Smith" value="<?= e($addFormData['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="jane@company.com" value="<?= e($addFormData['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Role</label>
          <select name="role" class="form-control">
            <option value="user"  <?= ($addFormData['role'] ?? 'user') === 'user'  ? 'selected' : '' ?>>Employee / User</option>
            <option value="admin" <?= ($addFormData['role'] ?? '')      === 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
            <button type="button" class="input-group-btn toggle-password">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('addUserModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" data-submit-btn>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add User
          <div class="spinner"></div>
        </button>
      </div>
    </form>
  </div>
</div>

<script src="<?= $root ?>assets/js/main.js"></script>
<?php if (!empty($addErrors)): ?>
<script>
  // Auto-open modal on validation error
  document.getElementById('addUserModal').classList.add('active');
</script>
<?php endif; ?>
</body>
</html>
