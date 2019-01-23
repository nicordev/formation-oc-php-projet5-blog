<?php
ob_start();
?>

<section id="blog-list">

    <h2 class="card-deck-title">Liste des articles du blog</h2>

    <div class="card-deck">
<?php
foreach ($posts as $post) {
?>
        <div class="card">
            <h3 class="card-title"><?= $post->getTitle() ?></h3>

            <div class="card-content"><?= $post->getExcerpt() ?></div>
        </div>
<?php
}
?>
    </div>
</section>
<?php

$content = ob_get_clean();

require 'main.php';