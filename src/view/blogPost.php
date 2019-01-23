<?php
$pageTitle = $post->getTitle();

// Content
ob_start();
?>
    <section class="blogpost">

        <h2 class="blogpost-title"><?= $post->getTitle() ?></h2>

        <div class="blogpost-content">
            <?= $post->getContent() ?>
        </div>

        <div class="blogpost-info">
            <?= $post->getCreationDate() ?>
        </div>

        <div class="blogpost-navigation">
            <ul>
                <?php
                if ($previousPostId) {
                    ?>
                    <li><a href="?page=blog-post&post-id=<?= $previousPostId ?>">Article précédent</a></li>
                    <?php
                }
                ?>

                <li><a href="?page=blog">Revenir à la liste des articles</a></li>

                <?php
                if ($nextPostId) {
                ?>
                    <li><a href="?page=blog-post&post-id=<?= $nextPostId ?>">Article suivant</a></li>
                <?php
                }
                ?>
            </ul>
        </div>
    </section>
<?php

$content = ob_get_clean();

require 'main.php';