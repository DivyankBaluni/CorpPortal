<?php
// includes/nav_admin.php – Admin sidebar navigation
// $activePage should be set before including: 'dashboard' | 'users' | 'profile'
$activePage = $activePage ?? '';
$root = rootUrl;
$adminName = e($_SESSION['name'] ?? 'Admin');
$initials   = strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1));
?>
<!-- ============  SIDEBAR  ============ -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
        <polygon points="12 2 2 7 12 12 22 7 12 2"/>
        <polyline points="2 17 12 22 22 17"/>
        <polyline points="2 12 12 17 22 12"/>
      </svg>
    </div>
    <span class="brand-name">CorpPortal</span>
    <button class="sidebar-close" id="sidebarClose" aria-label="Close menu">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M18 6L6 18M6 6l12 12"/>
      </svg>
    </button>
  </div>

  <div class="sidebar-role-badge">
    <span class="role-dot"></span> Administrator
  </div>

  <nav class="sidebar-nav">
    <p class="nav-section-label">Main</p>
    <a href="<?= $root ?>admin/dashboard.php"
       class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
      </svg>
      Dashboard
    </a>
    <a href="<?= $root ?>admin/users.php"
       class="nav-item <?= $activePage === 'users' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      User Management
    </a>

    <p class="nav-section-label">Account</p>
    <a href="<?= $root ?>admin/profile.php"
       class="nav-item <?= $activePage === 'profile' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
      </svg>
      My Profile
    </a>
    <a href="<?= $root ?>auth/logout.php" class="nav-item nav-logout">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      Logout
    </a>
  </nav>

  <div class="sidebar-user-footer">
    <div class="user-avatar-sm"><?= $initials ?></div>
    <div class="user-info-sm">
      <span class="user-name-sm"><?= $adminName ?></span>
      <span class="user-role-sm">Admin</span>
    </div>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
