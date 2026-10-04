<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getConnexion();
$errors = [];
$title = $url = $category = $tags = '';
$doublonDetecte = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $category = $_POST['category'] ?? '';
    $tags = trim($_POST['tags'] ?? '');
    $confirmerDoublon = isset($_POST['confirmer_doublon']);

    $errors = validerRessource($title, $url, $category);

    if (empty($errors) && !$confirmerDoublon) {
        $stmt = $pdo->prepare('SELECT id, title FROM links WHERE url = :url LIMIT 1');
        $stmt->execute(['url' => $url]);
        $existant = $stmt->fetch();
        if ($existant) {
            $doublonDetecte = $existant;
        }
    }

    if (empty($errors) && !$doublonDetecte) {
        $stmt = $pdo->prepare(
            'INSERT INTO links (title, url, category, tags, favorite) VALUES (:title, :url, :category, :tags, 0)'
        );
        $stmt->execute([
            'title' => $title,
            'url' => $url,
            'category' => $category,
            'tags' => $tags,
        ]);
        setFlash('success', 'Ressource ajoutée avec succès.');
        header('Location: resources.php');
        exit;
    }
}

$pageTitle = 'Ajouter une ressource';
$currentView = 'resources';
require __DIR__ . '/includes/header.php';
?>

<div class="view-heading">
  <h1>Ajouter une ressource</h1>
  <p class="view-subtitle">Sauvegardez un nouveau lien dans votre bibliothèque.</p>
</div>

<div class="form-card">
  <?php if ($doublonDetecte): ?>
    <div class="flash-message flash-warning">
      <i class="ri-error-warning-line"></i>
      Cette URL existe déjà sous le titre « <?= htmlspecialchars($doublonDetecte['title']) ?> ».
      Voulez-vous quand même l'ajouter ?
    </div>
  <?php endif; ?>

  <form method="POST" class="resource-form" novalidate>
    <div class="form-field">
      <label for="title">Titre <span class="required">*</span></label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="Ex. Guide complet de Docker">
      <?php if (!empty($errors['title'])): ?><p class="field-error"><?= htmlspecialchars($errors['title']) ?></p><?php endif; ?>
    </div>

    <div class="form-field">
      <label for="url">URL <span class="required">*</span></label>
      <input type="text" id="url" name="url" value="<?= htmlspecialchars($url) ?>" placeholder="https://exemple.com/article">
      <?php if (!empty($errors['url'])): ?><p class="field-error"><?= htmlspecialchars($errors['url']) ?></p><?php endif; ?>
    </div>

    <div class="form-field">
      <label for="category">Catégorie <span class="required">*</span></label>
      <select id="category" name="category">
        <option value="" disabled <?= $category === '' ? 'selected' : '' ?>>Choisir une catégorie</option>
        <?php foreach (CATEGORIES as $key => $label): ?>
          <option value="<?= $key ?>" <?= $category === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['category'])): ?><p class="field-error"><?= htmlspecialchars($errors['category']) ?></p><?php endif; ?>
    </div>

    <div class="form-field">
      <label for="tags">Tags</label>
      <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($tags) ?>" placeholder="docker, devops, conteneurs (séparés par des virgules)">
    </div>

    <?php if ($doublonDetecte): ?>
      <input type="hidden" name="confirmer_doublon" value="1">
    <?php endif; ?>

    <div class="form-actions">
      <a href="resources.php" class="btn btn-ghost">Annuler</a>
      <button type="submit" class="btn btn-primary"><?= $doublonDetecte ? 'Ajouter quand même' : 'Enregistrer' ?></button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
