<?php
$pageTitle = 'Liste des articles';

// Content
ob_start();
?>
<section class="blog-list">

    <h2 class="card-deck-title">Liste des articles du blog</h2>

    <div class="card-deck">
<?php
foreach ($posts as $post) {
?>
        <div class="card">
            <h3 class="card-title"><a href="<?= 'index.php?page=blog-post&post-id=' . $post->getId() ?>"><?= $post->getTitle() ?></a></h3>

            <div class="card-content"><?= $post->getExcerpt() ?></div>
            <div class="card-info"><?= $post->getCreationDate() ?></div>
        </div>
<?php
}
?>
    </div>
</section>
<?php

$content = ob_get_clean();

require 'main.php';