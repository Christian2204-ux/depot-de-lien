<?php
// Fonctions utilitaires partagées par toutes les pages.

const CATEGORIES = [
    'Videos' => 'Vidéos',
    'Tutoriels' => 'Tutoriels',
    'Formations' => 'Formations',
    'Documentation' => 'Documentation',
    'Articles' => 'Articles',
    'Outils' => 'Outils',
    'Autres' => 'Autres',
];

const CATEGORY_ICONS = [
    'Videos' => 'ri-play-circle-line',
    'Tutoriels' => 'ri-book-open-line',
    'Formations' => 'ri-graduation-cap-line',
    'Documentation' => 'ri-file-text-line',
    'Articles' => 'ri-article-line',
    'Outils' => 'ri-tools-line',
    'Autres' => 'ri-more-line',
];

function domaineDepuisUrl(string $url): string
{
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) {
        return $url;
    }
    return preg_replace('/^www\./', '', $host);
}

function formaterDate(string $dateStr): string
{
    $date = new DateTime($dateStr);
    $mois = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
    return $date->format('d') . ' ' . $mois[(int)$date->format('n') - 1] . ' ' . $date->format('Y');
}

function parseTags(?string $tagsString): array
{
    if (!$tagsString) {
        return [];
    }
    return array_filter(array_map('trim', explode(',', $tagsString)));
}

// Valide les champs d'une ressource. Retourne un tableau d'erreurs (vide si tout est valide).
function validerRessource(string $title, string $url, string $category): array
{
    $errors = [];

    if (trim($title) === '') {
        $errors['title'] = 'Le titre est obligatoire.';
    }

    if (trim($url) === '') {
        $errors['url'] = "L'URL est obligatoire.";
    } elseif (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
        $errors['url'] = "L'URL n'est pas valide (elle doit commencer par http:// ou https://).";
    }

    if (!array_key_exists($category, CATEGORIES)) {
        $errors['category'] = 'La catégorie est obligatoire et doit être valide.';
    }

    return $errors;
}

// Stocke un message flash en session, affiché une seule fois sur la page suivante.
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
