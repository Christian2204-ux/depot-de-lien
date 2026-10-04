<?php
// Attend que $pageTitle et $currentView soient définis par la page appelante.
require_once __DIR__ . '/functions.php';

$pdo = getConnexion();
$categoryCounts = [];
foreach (array_keys(CATEGORIES) as $cat) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM links WHERE category = :cat');
    $stmt->execute(['cat' => $cat]);
    $categoryCounts[$cat] = (int)$stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'LinkVault') ?> — LinkVault</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="app-shell">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <span class="brand-mark"><i class="ri-links-line"></i></span>
      <span class="brand-name">LinkVault</span>
    </div>

    <nav class="sidebar-nav" aria-label="Navigation principale">
      <a href="index.php" class="nav-link <?= ($currentView ?? '') === 'dashboard' ? 'active' : '' ?>">
        <i class="ri-dashboard-line"></i><span>Tableau de bord</span>
      </a>
      <a href="resources.php" class="nav-link <?= ($currentView ?? '') === 'resources' ? 'active' : '' ?>">
        <i class="ri-stack-line"></i><span>Toutes les ressources</span>
      </a>
      <a href="favorites.php" class="nav-link <?= ($currentView ?? '') === 'favorites' ? 'active' : '' ?>">
        <i class="ri-star-line"></i><span>Favoris</span>
      </a>
      <a href="categories.php" class="nav-link <?= ($currentView ?? '') === 'categories' ? 'active' : '' ?>">
        <i class="ri-folder-3-line"></i><span>Catégories</span>
      </a>
    </nav>

    <div class="sidebar-categories">
      <p class="sidebar-subtitle">Catégories</p>
      <ul class="sidebar-category-list">
        <?php foreach (CATEGORIES as $key => $label): ?>
          <li>
            <a href="resources.php?category=<?= urlencode($key) ?>">
              <span><?= htmlspecialchars($label) ?></span>
              <span class="cat-count"><?= $categoryCounts[$key] ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="sidebar-footer">
      <button type="button" class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
        <i class="ri-moon-line"></i><span>Mode sombre</span>
      </button>
      <a href="logout.php" class="nav-link nav-link-logout">
        <i class="ri-logout-box-line"></i><span>Déconnexion</span>
      </a>
    </div>
  </aside>

  <nav class="mobile-bottom-nav" aria-label="Navigation mobile">
    <a href="index.php" class="<?= ($currentView ?? '') === 'dashboard' ? 'active' : '' ?>">
      <i class="ri-dashboard-line"></i><span>Accueil</span>
    </a>
    <a href="resources.php" class="<?= ($currentView ?? '') === 'resources' ? 'active' : '' ?>">
      <i class="ri-stack-line"></i><span>Ressources</span>
    </a>
    <a href="favorites.php" class="<?= ($currentView ?? '') === 'favorites' ? 'active' : '' ?>">
      <i class="ri-star-line"></i><span>Favoris</span>
    </a>
    <a href="categories.php" class="<?= ($currentView ?? '') === 'categories' ? 'active' : '' ?>">
      <i class="ri-folder-3-line"></i><span>Catégories</span>
    </a>
  </nav>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="main-column">
    <header class="mobile-topbar">
      <div class="mobile-topbar-main">
        <button class="icon-btn" id="menuToggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="sidebar">
          <i class="ri-menu-line"></i>
        </button>
        <div class="mobile-brand">
          <span class="brand-mark"><i class="ri-links-line"></i></span>
          <span class="brand-name">LinkVault</span>
        </div>
        <a href="add.php" class="icon-btn mobile-add-btn" aria-label="Ajouter une ressource">
          <i class="ri-add-line"></i>
        </a>
      </div>
      <form action="resources.php" method="GET" class="search-bar mobile-search-bar">
        <i class="ri-search-line" aria-hidden="true"></i>
        <input type="search" name="search" placeholder="Rechercher une ressource" aria-label="Rechercher une ressource">
      </form>
    </header>
    <header class="app-header">
      <form action="resources.php" method="GET" class="search-bar">
        <i class="ri-search-line" aria-hidden="true"></i>
        <input type="search" name="search" placeholder="Rechercher un titre, une URL, un tag..." aria-label="Rechercher une ressource">
      </form>
      <a href="add.php" class="btn btn-primary">
        <i class="ri-add-line"></i><span>Ajouter une ressource</span>
      </a>
    </header>

    <main class="content">
      <?php $flash = getFlash(); ?>
      <?php if ($flash): ?>
        <div class="flash-message flash-<?= htmlspecialchars($flash['type']) ?>">
          <i class="<?= $flash['type'] === 'error' ? 'ri-error-warning-line' : 'ri-checkbox-circle-line' ?>"></i>
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>
