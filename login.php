<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Si déjà connecté, redirige directement vers le tableau de bord.
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $pdo = getConnexion();
        $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        }

        $erreur = 'Identifiant ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion — LinkVault</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">

<div class="login-card">
  <div class="login-brand">
    <span class="brand-mark"><i class="ri-links-line"></i></span>
    <span class="brand-name">LinkVault</span>
  </div>
  <p class="login-subtitle">Connectez-vous pour accéder à vos ressources.</p>

  <?php if ($erreur): ?>
    <p class="login-error"><i class="ri-error-warning-line"></i> <?= htmlspecialchars($erreur) ?></p>
  <?php endif; ?>

  <form method="POST" class="login-form">
    <div class="form-field">
      <label for="username">Identifiant</label>
      <input type="text" id="username" name="username" required autofocus>
    </div>
    <div class="form-field">
      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
  </form>
</div>

</body>
</html>
