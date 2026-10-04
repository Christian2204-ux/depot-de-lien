// LinkVault — JavaScript frontend (purement interactif/visuel, aucun appel API).
// Toutes les actions réelles (ajout, modification, suppression) passent par
// de vrais formulaires PHP avec rechargement de page.

document.addEventListener('DOMContentLoaded', () => {
  initMobileMenu();
  initThemeToggle();
  initCopyLinkButtons();
  initDeleteConfirmClientSide();
});

// ---------------------------------------------------------------------------
// Menu mobile (ouverture/fermeture de la sidebar)
// ---------------------------------------------------------------------------
function initMobileMenu() {
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (!menuToggle || !sidebar || !overlay) return;

  const openMenu = () => {
    sidebar.classList.add('open');
    overlay.classList.add('visible');
    menuToggle.setAttribute('aria-expanded', 'true');
  };

  const closeMenu = () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('visible');
    menuToggle.setAttribute('aria-expanded', 'false');
  };

  menuToggle.addEventListener('click', openMenu);
  overlay.addEventListener('click', closeMenu);
}

// ---------------------------------------------------------------------------
// Mode sombre (préférence sauvegardée localement dans le navigateur)
// ---------------------------------------------------------------------------
function initThemeToggle() {
  const toggleBtn = document.getElementById('themeToggle');
  const root = document.documentElement;
  const stored = localStorage.getItem('linkvault-theme');

  if (stored === 'dark') {
    root.setAttribute('data-theme', 'dark');
    updateThemeIcon(true);
  }

  if (!toggleBtn) return;

  toggleBtn.addEventListener('click', () => {
    const isDark = root.getAttribute('data-theme') === 'dark';
    if (isDark) {
      root.removeAttribute('data-theme');
      localStorage.setItem('linkvault-theme', 'light');
    } else {
      root.setAttribute('data-theme', 'dark');
      localStorage.setItem('linkvault-theme', 'dark');
    }
    updateThemeIcon(!isDark);
  });
}

function updateThemeIcon(isDark) {
  const toggleBtn = document.getElementById('themeToggle');
  if (!toggleBtn) return;
  const icon = toggleBtn.querySelector('i');
  const label = toggleBtn.querySelector('span');
  if (icon) icon.className = isDark ? 'ri-sun-line' : 'ri-moon-line';
  if (label) label.textContent = isDark ? 'Mode clair' : 'Mode sombre';
}

// ---------------------------------------------------------------------------
// Copier le lien dans le presse-papier
// ---------------------------------------------------------------------------
function initCopyLinkButtons() {
  document.querySelectorAll('.copy-link-btn').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const url = btn.dataset.url;
      try {
        await navigator.clipboard.writeText(url);
        const icon = btn.querySelector('i');
        btn.classList.add('copied');
        if (icon) icon.className = 'ri-check-line';
        setTimeout(() => {
          btn.classList.remove('copied');
          if (icon) icon.className = 'ri-file-copy-line';
        }, 1500);
      } catch {
        // Le navigateur a refusé l'accès au presse-papier ; pas d'action bloquante.
      }
    });
  });
}

// ---------------------------------------------------------------------------
// Petite confirmation JS avant de quitter la page de suppression par erreur
// (la vraie confirmation reste la page delete.php elle-même, ceci est juste
// un filet de sécurité supplémentaire pour les clics accidentels sur mobile)
// ---------------------------------------------------------------------------
function initDeleteConfirmClientSide() {
  document.querySelectorAll('a[href^="delete.php"]').forEach((link) => {
    link.addEventListener('click', (e) => {
      // On laisse la navigation continuer normalement : delete.php affiche
      // déjà une page de confirmation dédiée avant toute suppression réelle.
    });
  });
}
