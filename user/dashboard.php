<?php
// ============================================================
//  user/dashboard.php
// ============================================================

require_once __DIR__ . '/../includes/session_check.php';
requireRole('user');
require_once __DIR__ . '/../config/db.php';

// Always fetch fresh from DB
$stmt = $pdo->prepare('SELECT id,name,email,role,created_at FROM users WHERE id=? LIMIT 1');
$stmt->execute([$_SESSION['user_id']]);
$me = $stmt->fetch();

$activePage = 'dashboard';
$root = rootUrl;
$initials = strtoupper(substr($me['name'] ?? 'U', 0, 1));
$hour = (int)date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard – CorpPortal</title>
<link rel="stylesheet" href="<?= $root ?>assets/css/style.css">
</head>
<body>
<div class="app-layout">
  <?php require_once __DIR__ . '/../includes/nav_user.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <button class="topbar-menu-btn" id="menuBtn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div>
          <div class="breadcrumb">
            <span>CorpPortal</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="current">Dashboard</span>
          </div>
          <div class="page-title">My Dashboard</div>
        </div>
      </div>
      <div class="topbar-right">
        <span class="topbar-greeting">Hello, <strong><?= e($me['name']) ?></strong></span>
        <div class="topbar-avatar employee" title="My Profile" onclick="window.location='<?= $root ?>user/profile.php'"><?= $initials ?></div>
      </div>
    </div>

    <div class="page-body">

      <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['msg']) ?></div>
      <?php endif; ?>

      <!-- Welcome Banner (coral for user) -->
      <div class="welcome-banner" style="background:linear-gradient(135deg,#c2410c 0%,#7c2d12 100%)">
        <div class="welcome-text">
          <h2><?= $greeting ?>, <?= e($me['name']) ?>! 👋</h2>
          <p>Welcome to your employee portal. Here's your overview.</p>
        </div>
        <div class="welcome-emoji">💼</div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon coral">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value"><?= e($me['name']) ?></div>
            <div class="stat-label">Your Name</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon teal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
              <polyline points="22,6 12,13 2,6"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" style="font-size:1rem"><?= e($me['email']) ?></div>
            <div class="stat-label">Email Address</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" style="font-size:1.1rem">Employee</div>
            <div class="stat-label">Your Role</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon slate">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" style="font-size:1rem"><?= date('M j, Y', strtotime($me['created_at'])) ?></div>
            <div class="stat-label">Joined</div>
          </div>
        </div>
      </div>

      <!-- Quick Info Card -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--coral-500)" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            My Information
          </div>
          <a href="<?= $root ?>user/profile.php" class="btn btn-outline btn-sm">Edit Profile</a>
        </div>
        <div class="card-body">
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:20px">
            <div>
              <div class="info-field-label">Full Name</div>
              <div class="info-field-value" style="font-size:.95rem;color:var(--slate-800);font-weight:600;margin-top:4px"><?= e($me['name']) ?></div>
            </div>
            <div>
              <div class="info-field-label">Email</div>
              <div class="info-field-value" style="font-size:.95rem;color:var(--slate-800);font-weight:600;margin-top:4px"><?= e($me['email']) ?></div>
            </div>
            <div>
              <div class="info-field-label">Role</div>
              <div style="margin-top:4px"><span class="badge badge-user">Employee</span></div>
            </div>
            <div>
              <div class="info-field-label">Member Since</div>
              <div class="info-field-value" style="font-size:.95rem;color:var(--slate-800);font-weight:600;margin-top:4px">
                <?= date('F j, Y', strtotime($me['created_at'])) ?>
              </div>
            </div>
            <div>
              <div class="info-field-label">User ID</div>
              <div class="info-field-value" style="font-size:.95rem;color:var(--slate-800);font-weight:600;margin-top:4px">#<?= $me['id'] ?></div>
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
