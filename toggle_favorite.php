<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$pdo = getConnexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $redirect = $_POST['redirect'] ?? 'index.php';

    $stmt = $pdo->prepare('SELECT favorite FROM links WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $current = $stmt->fetchColumn();

    if ($current !== false) {
        $nouveauStatut = $current ? 0 : 1;
        $stmt = $pdo->prepare('UPDATE links SET favorite = :fav WHERE id = :id');
        $stmt->execute(['fav' => $nouveauStatut, 'id' => $id]);
    }

    // Sécurité simple : n'autorise que les redirections internes.
    $redirect = preg_match('/^[a-zA-Z0-9_.\/?=&%-]+$/', $redirect) ? $redirect : 'index.php';
    header('Location: ' . $redirect);
    exit;
}

header('Location: index.php');
exit;
