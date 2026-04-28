<?php
// includes/nav_user.php – User sidebar navigation
$activePage = $activePage ?? '';
$root = rootUrl;
$userName = e($_SESSION['name'] ?? 'User');
$initials   = strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1));
?>
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

  <div class="sidebar-role-badge employee">
    <span class="role-dot"></span> Employee
  </div>

  <nav class="sidebar-nav">
    <p class="nav-section-label">Main</p>
    <a href="<?= $root ?>user/dashboard.php"
       class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
      </svg>
      Dashboard
    </a>

    <p class="nav-section-label">Account</p>
    <a href="<?= $root ?>user/profile.php"
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
    <div class="user-avatar-sm employee"><?= $initials ?></div>
    <div class="user-info-sm">
      <span class="user-name-sm"><?= $userName ?></span>
      <span class="user-role-sm">Employee</span>
    </div>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
