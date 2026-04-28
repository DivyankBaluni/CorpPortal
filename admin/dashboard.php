<?php
// ============================================================
//  admin/dashboard.php
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';
requireRole('admin');
require_once __DIR__ . '/../config/db.php';

// Stats
$totalUsers  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$totalAll    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Recent 5 users
$recent = $pdo->query("SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

$activePage = 'dashboard';
$root = rootUrl;
$initials = strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard – Admin · CorpPortal</title>
<link rel="stylesheet" href="<?= $root ?>assets/css/style.css">
</head>
<body>
<div class="app-layout">

  <?php require_once __DIR__ . '/../includes/nav_admin.php'; ?>

  <main class="main-content">
    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-left">
        <button class="topbar-menu-btn" id="menuBtn" aria-label="Open menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
        <div>
          <div class="breadcrumb">
            <span>CorpPortal</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="current">Dashboard</span>
          </div>
          <div class="page-title">Admin Dashboard</div>
        </div>
      </div>
      <div class="topbar-right">
        <span class="topbar-greeting">Hello, <strong><?= e($_SESSION['name']) ?></strong></span>
        <div class="topbar-avatar" title="My Profile" onclick="window.location='<?= $root ?>admin/profile.php'"><?= $initials ?></div>
      </div>
    </div>

    <!-- Page Body -->
    <div class="page-body">

      <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
      <?php endif; ?>

      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <div class="welcome-text">
          <h2>Good to see you, <?= e($_SESSION['name']) ?>!</h2>
          <p>Here's what's happening in your portal today.</p>
        </div>
        <div class="welcome-emoji">🏢</div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value"><?= $totalAll ?></div>
            <div class="stat-label">Total Members</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon coral">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-label">Employees</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value"><?= $totalAdmins ?></div>
            <div class="stat-label">Admins</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon slate">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value"><?= date('D') ?></div>
            <div class="stat-label"><?= date('M j, Y') ?></div>
          </div>
        </div>
      </div>

      <!-- Recent Users Table -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--teal-600)" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Recent Members
          </div>
          <a href="<?= $root ?>admin/users.php" class="btn btn-outline btn-sm">
            View All
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        </div>
        <div class="card-body" style="padding-top:16px">
          <div class="table-wrapper">
            <table class="table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Role</th>
                  <th>Joined</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recent)): ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--slate-400);padding:32px">No users found.</td></tr>
                <?php else: ?>
                  <?php foreach ($recent as $u): ?>
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
                      <td>
                        <span class="badge badge-<?= $u['role'] ?>">
                          <?= ucfirst($u['role']) ?>
                        </span>
                      </td>
                      <td style="color:var(--slate-500)"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                      <td>
                        <div class="action-group">
                          <a href="<?= $root ?>admin/edit_user.php?id=<?= $u['id'] ?>"
                             class="btn btn-outline btn-sm btn-icon" title="Edit">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                          </a>
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

    </div><!-- /page-body -->
  </main>
</div>
<script src="<?= $root ?>assets/js/main.js"></script>
</body>
</html>
