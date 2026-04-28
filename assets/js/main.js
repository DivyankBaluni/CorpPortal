// ============================================================
//  CorpPortal – main.js
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  /* ── Sidebar toggle (mobile) ─────────────────────────── */
  const sidebar        = document.getElementById('sidebar');
  const sidebarClose   = document.getElementById('sidebarClose');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const menuBtn        = document.getElementById('menuBtn');

  function openSidebar() {
    sidebar?.classList.add('open');
    sidebarOverlay?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar?.classList.remove('open');
    sidebarOverlay?.classList.remove('active');
    document.body.style.overflow = '';
  }

  menuBtn?.addEventListener('click', openSidebar);
  sidebarClose?.addEventListener('click', closeSidebar);
  sidebarOverlay?.addEventListener('click', closeSidebar);

  /* ── Password visibility toggle ─────────────────────── */
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.closest('.input-group')?.querySelector('input');
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.innerHTML = show
        ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`
        : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
    });
  });

  /* ── Modal helpers ───────────────────────────────────── */
  window.openModal = function(id) {
    const overlay = document.getElementById(id);
    overlay?.classList.add('active');
  };

  window.closeModal = function(id) {
    const overlay = document.getElementById(id);
    overlay?.classList.remove('active');
  };

  // Close on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
      if (e.target === overlay) overlay.classList.remove('active');
    });
  });

  /* ── Delete confirmation ─────────────────────────────── */
  document.querySelectorAll('[data-delete-form]').forEach(btn => {
    btn.addEventListener('click', () => {
      const formId = btn.dataset.deleteForm;
      if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById(formId)?.submit();
      }
    });
  });

  /* ── Live search filter for users table ─────────────── */
  const searchInput = document.getElementById('userSearch');
  const tableRows   = document.querySelectorAll('#usersTable tbody tr');

  searchInput?.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase();
    tableRows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(q) ? '' : 'none';
    });
  });

  /* ── Auto-dismiss flash messages ────────────────────── */
  const flash = document.querySelector('.alert');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .4s, transform .4s';
      flash.style.opacity = '0';
      flash.style.transform = 'translateY(-6px)';
      setTimeout(() => flash.remove(), 400);
    }, 4000);
  }

  /* ── Form loading state ──────────────────────────────── */
  document.querySelectorAll('form[data-loading]').forEach(form => {
    form.addEventListener('submit', () => {
      const btn     = form.querySelector('[data-submit-btn]');
      const spinner = form.querySelector('.spinner');
      if (btn) {
        btn.disabled = true;
        btn.style.opacity = '.75';
      }
      if (spinner) spinner.style.display = 'inline-block';
    });
  });

});
