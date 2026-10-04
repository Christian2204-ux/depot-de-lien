<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getConnexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM links WHERE id = :id');
    $stmt->execute(['id' => $id]);
    setFlash('success', 'Ressource supprimée.');
    header('Location: resources.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM links WHERE id = :id');
$stmt->execute(['id' => $id]);
$link = $stmt->fetch();

if (!$link) {
    setFlash('error', 'Ressource introuvable.');
    header('Location: resources.php');
    exit;
}

$pageTitle = 'Supprimer la ressource';
$currentView = 'resources';
require __DIR__ . '/includes/header.php';
?>

<div class="view-heading">
  <h1>Supprimer cette ressource ?</h1>
  <p class="view-subtitle">Cette action est définitive.</p>
</div>

<div class="form-card confirm-card">
  <p>Vous êtes sur le point de supprimer « <strong><?= htmlspecialchars($link['title']) ?></strong> ». Cette action ne peut pas être annulée.</p>

  <form method="POST" class="form-actions">
    <input type="hidden" name="id" value="<?= $id ?>">
    <a href="resources.php" class="btn btn-ghost">Annuler</a>
    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
