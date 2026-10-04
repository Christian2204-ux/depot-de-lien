<?php
// Protège une page : redirige vers la connexion si l'utilisateur n'est pas authentifié.
// À inclure tout en haut de chaque page qui nécessite d'être connecté.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
