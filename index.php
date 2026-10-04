<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/card.php';

$pdo = getConnexion();

$total = (int)$pdo->query('SELECT COUNT(*) FROM links')->fetchColumn();
$favorisCount = (int)$pdo->query('SELECT COUNT(*) FROM links WHERE favorite = 1')->fetchColumn();

$statsParCategorie = [];
foreach (['Videos', 'Tutoriels', 'Formations', 'Documentation'] as $cat) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM links WHERE category = :cat');
    $stmt->execute(['cat' => $cat]);
    $statsParCategorie[$cat] = (int)$stmt->fetchColumn();
}

$recentes = $pdo->query('SELECT * FROM links ORDER BY created_at DESC LIMIT 6')->fetchAll();

$pageTitle = 'Tableau de bord';
$currentView = 'dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="view-heading">
  <h1>Tableau de bord</h1>
  <p class="view-subtitle">Vue d'ensemble de vos ressources sauvegardées.</p>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <span class="stat-icon"><i class="ri-stack-line"></i></span>
    <span class="stat-value"><?= $total ?></span>
    <span class="stat-label">Ressources totales</span>
  </div>
  <div class="stat-card">
    <span class="stat-icon"><i class="ri-star-line"></i></span>
    <span class="stat-value"><?= $favorisCount ?></span>
    <span class="stat-label">Favoris</span>
  </div>
  <div class="stat-card">
    <span class="stat-icon"><i class="ri-play-circle-line"></i></span>
    <span class="stat-value"><?= $statsParCategorie['Videos'] ?></span>
    <span class="stat-label">Vidéos</span>
  </div>
  <div class="stat-card">
    <span class="stat-icon"><i class="ri-book-open-line"></i></span>
    <span class="stat-value"><?= $statsParCategorie['Tutoriels'] ?></span>
    <span class="stat-label">Tutoriels</span>
  </div>
  <div class="stat-card">
    <span class="stat-icon"><i class="ri-graduation-cap-line"></i></span>
    <span class="stat-value"><?= $statsParCategorie['Formations'] ?></span>
    <span class="stat-label">Formations</span>
  </div>
  <div class="stat-card">
    <span class="stat-icon"><i class="ri-file-text-line"></i></span>
    <span class="stat-value"><?= $statsParCategorie['Documentation'] ?></span>
    <span class="stat-label">Documentation</span>
  </div>
</div>

<div class="section-block">
  <div class="section-block-heading">
    <h2>Ajoutées récemment</h2>
    <a href="resources.php" class="link-quiet">Tout voir</a>
  </div>
  <div class="cards-grid">
    <?php if (empty($recentes)): ?>
      <p class="empty-state">Aucune ressource pour le moment. Ajoutez-en une pour commencer.</p>
    <?php else: ?>
      <?php foreach ($recentes as $link): afficherCarte($link); endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
