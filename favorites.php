<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/card.php';

$pdo = getConnexion();
$favoris = $pdo->query('SELECT * FROM links WHERE favorite = 1 ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Favoris';
$currentView = 'favorites';
require __DIR__ . '/includes/header.php';
?>

<div class="view-heading">
  <h1>Favoris</h1>
  <p class="view-subtitle">Vos ressources marquées comme favorites.</p>
</div>

<div class="cards-grid">
  <?php if (empty($favoris)): ?>
    <p class="empty-state">Vous n'avez encore aucun favori.</p>
  <?php else: ?>
    <?php foreach ($favoris as $link): afficherCarte($link); endforeach; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
