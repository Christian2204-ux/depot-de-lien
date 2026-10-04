<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/card.php';

$pdo = getConnexion();

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';
$page = max(1, (int)($_GET['page'] ?? 1));
$parPage = 12;

$conditions = [];
$params = [];

if ($search !== '') {
    $conditions[] = '(title LIKE :s1 OR url LIKE :s2 OR category LIKE :s3 OR tags LIKE :s4)';
    $terme = '%' . $search . '%';
    $params['s1'] = $terme;
    $params['s2'] = $terme;
    $params['s3'] = $terme;
    $params['s4'] = $terme;
}

if ($category !== '' && array_key_exists($category, CATEGORIES)) {
    $conditions[] = 'category = :cat';
    $params['cat'] = $category;
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$orderMap = [
    'date_desc' => 'created_at DESC',
    'date_asc' => 'created_at ASC',
    'title_asc' => 'title ASC',
    'category' => 'category ASC, title ASC',
];
$orderBy = $orderMap[$sort] ?? $orderMap['date_desc'];

// Compte total pour la pagination
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM links $whereClause");
$stmtCount->execute($params);
$totalResultats = (int)$stmtCount->fetchColumn();
$totalPages = max(1, (int)ceil($totalResultats / $parPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $parPage;

$stmt = $pdo->prepare("SELECT * FROM links $whereClause ORDER BY $orderBy LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue('limit', $parPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$resultats = $stmt->fetchAll();

// Conserve les filtres actuels dans les liens de pagination / tri
function construireUrl(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    return 'resources.php?' . http_build_query($params);
}

$pageTitle = 'Toutes les ressources';
$currentView = 'resources';
require __DIR__ . '/includes/header.php';
?>

<div class="view-heading">
  <h1>Toutes les ressources</h1>
  <p class="view-subtitle">Recherchez et filtrez l'ensemble de vos ressources.</p>
</div>

<div class="toolbar">
  <div class="filters-bar" role="group" aria-label="Filtrer par catégorie">
    <a href="<?= construireUrl(['category' => '', 'page' => 1]) ?>" class="filter-chip <?= $category === '' ? 'active' : '' ?>">Toutes</a>
    <?php foreach (CATEGORIES as $key => $label): ?>
      <a href="<?= construireUrl(['category' => $key, 'page' => 1]) ?>" class="filter-chip <?= $category === $key ? 'active' : '' ?>"><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
  </div>

  <form method="GET" class="sort-form">
    <?php if ($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
    <?php if ($category): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
    <label for="sort" class="sort-label">Trier par</label>
    <select id="sort" name="sort" onchange="this.form.submit()">
      <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Plus récent</option>
      <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Plus ancien</option>
      <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Titre (A-Z)</option>
      <option value="category" <?= $sort === 'category' ? 'selected' : '' ?>>Catégorie</option>
    </select>
  </form>
</div>

<?php if ($search): ?>
  <p class="search-summary">Résultats pour « <?= htmlspecialchars($search) ?> » (<?= $totalResultats ?>)
    <a href="<?= construireUrl(['search' => '', 'page' => 1]) ?>" class="link-quiet">Effacer</a>
  </p>
<?php endif; ?>

<div class="cards-grid">
  <?php if (empty($resultats)): ?>
    <p class="empty-state">Aucune ressource ne correspond à votre recherche.</p>
  <?php else: ?>
    <?php foreach ($resultats as $link): afficherCarte($link); endforeach; ?>
  <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
  <nav class="pagination" aria-label="Pagination">
    <a href="<?= construireUrl(['page' => max(1, $page - 1)]) ?>" class="pagination-btn <?= $page <= 1 ? 'disabled' : '' ?>">
      <i class="ri-arrow-left-s-line"></i> Précédent
    </a>
    <span class="pagination-info">Page <?= $page ?> sur <?= $totalPages ?></span>
    <a href="<?= construireUrl(['page' => min($totalPages, $page + 1)]) ?>" class="pagination-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
      Suivant <i class="ri-arrow-right-s-line"></i>
    </a>
  </nav>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
