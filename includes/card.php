<?php
// Affiche une carte de ressource. Attend un tableau $link (une ligne de la table links).
function afficherCarte(array $link): void
{
    $tags = parseTags($link['tags']);
    $domaine = domaineDepuisUrl($link['url']);
    $categoryLabel = CATEGORIES[$link['category']] ?? $link['category'];
    ?>
    <article class="resource-card">
      <div class="resource-card-top">
        <span class="resource-category"><?= htmlspecialchars($categoryLabel) ?></span>
        <div class="card-actions">
          <form action="toggle_favorite.php" method="POST" class="inline-form">
            <input type="hidden" name="id" value="<?= (int)$link['id'] ?>">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
            <button type="submit" class="card-icon-btn favorite-btn <?= $link['favorite'] ? 'is-favorite' : '' ?>" aria-label="<?= $link['favorite'] ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>">
              <i class="<?= $link['favorite'] ? 'ri-star-fill' : 'ri-star-line' ?>"></i>
            </button>
          </form>
          <button type="button" class="card-icon-btn copy-link-btn" data-url="<?= htmlspecialchars($link['url']) ?>" aria-label="Copier le lien">
            <i class="ri-file-copy-line"></i>
          </button>
          <a href="edit.php?id=<?= (int)$link['id'] ?>" class="card-icon-btn" aria-label="Modifier">
            <i class="ri-pencil-line"></i>
          </a>
          <a href="delete.php?id=<?= (int)$link['id'] ?>" class="card-icon-btn" aria-label="Supprimer">
            <i class="ri-delete-bin-line"></i>
          </a>
        </div>
      </div>

      <h3 class="resource-title"><?= htmlspecialchars($link['title']) ?></h3>
      <p class="resource-domain"><i class="ri-link"></i><?= htmlspecialchars($domaine) ?></p>

      <?php if ($tags): ?>
        <div class="resource-tags">
          <?php foreach ($tags as $tag): ?>
            <span class="tag-chip"><?= htmlspecialchars($tag) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="resource-card-footer">
        <span class="resource-date"><?= formaterDate($link['created_at']) ?></span>
        <a class="open-link-btn" href="<?= htmlspecialchars($link['url']) ?>" target="_blank" rel="noopener noreferrer">
          Ouvrir <i class="ri-arrow-right-up-line"></i>
        </a>
      </div>
    </article>
    <?php
}
