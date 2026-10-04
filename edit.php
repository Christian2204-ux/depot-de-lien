<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getConnexion();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM links WHERE id = :id');
$stmt->execute(['id' => $id]);
$link = $stmt->fetch();

if (!$link) {
    setFlash('error', 'Ressource introuvable.');
    header('Location: resources.php');
    exit;
}

$errors = [];
$title = $link['title'];
$url = $link['url'];
$category = $link['category'];
$tags = $link['tags'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $category = $_POST['category'] ?? '';
    $tags = trim($_POST['tags'] ?? '');

    $errors = validerRessource($title, $url, $category);

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'UPDATE links SET title = :title, url = :url, category = :category, tags = :tags WHERE id = :id'
        );
        $stmt->execute([
            'title' => $title,
            'url' => $url,
            'category' => $category,
            'tags' => $tags,
            'id' => $id,
        ]);
        setFlash('success', 'Ressource mise à jour.');
        header('Location: resources.php');
        exit;
    }
}

$pageTitle = 'Modifier la ressource';
$currentView = 'resources';
require __DIR__ . '/includes/header.php';
?>

<div class="view-heading">
  <h1>Modifier la ressource</h1>
  <p class="view-subtitle">Mettez à jour les informations de ce lien.</p>
</div>

<div class="form-card">
  <form method="POST" class="resource-form" novalidate>
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="form-field">
      <label for="title">Titre <span class="required">*</span></label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>">
      <?php if (!empty($errors['title'])): ?><p class="field-error"><?= htmlspecialchars($errors['title']) ?></p><?php endif; ?>
    </div>

    <div class="form-field">
      <label for="url">URL <span class="required">*</span></label>
      <input type="text" id="url" name="url" value="<?= htmlspecialchars($url) ?>">
      <?php if (!empty($errors['url'])): ?><p class="field-error"><?= htmlspecialchars($errors['url']) ?></p><?php endif; ?>
    </div>

    <div class="form-field">
      <label for="category">Catégorie <span class="required">*</span></label>
      <select id="category" name="category">
        <?php foreach (CATEGORIES as $key => $label): ?>
          <option value="<?= $key ?>" <?= $category === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['category'])): ?><p class="field-error"><?= htmlspecialchars($errors['category']) ?></p><?php endif; ?>
    </div>

    <div class="form-field">
      <label for="tags">Tags</label>
      <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($tags) ?>">
    </div>

    <div class="form-actions">
      <a href="resources.php" class="btn btn-ghost">Annuler</a>
      <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
