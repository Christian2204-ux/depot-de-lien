<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getConnexion();

$counts = [];
foreach (array_keys(CATEGORIES) as $cat) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM links WHERE category = :cat');
    $stmt->execute(['cat' => $cat]);
    $counts[$cat] = (int)$stmt->fetchColumn();
}

$pageTitle = 'Catégories';
$currentView = 'categories';
require __DIR__ . '/includes/header.php';
?>

<div class="view-heading">
  <h1>Catégories</h1>
  <p class="view-subtitle">Parcourez vos ressources par catégorie.</p>
</div>

<div class="categories-grid">
  <?php foreach (CATEGORIES as $key => $label): ?>
    <a href="resources.php?category=<?= urlencode($key) ?>" class="category-tile">
      <span class="cat-icon"><i class="<?= CATEGORY_ICONS[$key] ?>"></i></span>
      <span class="cat-name"><?= htmlspecialchars($label) ?></span>
      <span class="cat-count-text"><?= $counts[$key] ?> ressource<?= $counts[$key] > 1 ? 's' : '' ?></span>
    </a>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
