<?php
$pageTitle = 'Editeur d\'article';

// Header
ob_start();
require 'adminHeader.php';
$header = ob_get_clean();

// Content
ob_start();
?>
    <section class="blog-admin">
        <h2>Editeur d'article</h2>

        <div>
            <?php
            require 'message.php';
            ?>
        </div>

        <?php
        if ($postToEdit) {
            ?>
            <div>
                <form action="?page=blog-admin" method="post">
                    <input type="hidden" name="delete-post" value="<?= $postToEdit->getId() ?>">
                    <p>
                        <input class="btn" type="submit" value="Supprimer l'article">
                    </p>
                </form>
            </div>
            <?php
        }
        ?>

        <div>
            <form action="?page=blog-admin" method="post">
                <!-- Hidden fields -->
                <?php
                if ($postToEdit) {
                    ?>
                    <input type="hidden" name="edit-post" value="<?= $postToEdit->getId() ?>">
                    <input type="hidden" name="post-editor-id" value="2"><!-- TODO put the true editor id here -->
                    <?php
                } else {
                    ?>
                    <input type="hidden" name="add-post">
                    <?php
                }
                ?>
                <input type="hidden" name="post-author-id" value="1"><!-- TODO put the true author id here -->

                <!-- Visible fields-->
                <p>
                    <input class="btn" type="submit" value="Publier l'article">
                </p>
                <p>
                    <label for="post-title">Titre de l'article</label><br>
                    <input class="admin-input" type="text" name="post-title" id="post-title" value="<?= $postToEdit !== null ? $postToEdit->getTitle() : '' ?>">
                </p>
                <p>
                    <label for="post-excerpt">Extrait de l'article (ou chapo)</label><br>
                    <textarea class="admin-textarea" name="post-excerpt" id="post-excerpt" cols="30" rows="5"><?= $postToEdit !== null ? $postToEdit->getExcerpt() : '' ?></textarea>
                </p>
                <p>
                    <label for="post-content">Contenu de l'article</label><br>
                    <textarea class="admin-textarea" name="post-content" id="post-content" cols="30" rows="30"><?= $postToEdit !== null ? $postToEdit->getContent() : '' ?></textarea>
                </p>
            </form>
        </div>
    </section>
<?php

$content = ob_get_clean();

// Footer
ob_start();
require 'adminFooter.php';
$footer = ob_get_clean();

require 'main.php';