<?php
$pageTitle = 'Ajout d\'un article';

// Header
ob_start();
require 'adminHeader.php';
$header = ob_get_clean();

// Content
ob_start();
?>
    <section class="blog-admin">
        <h2>Création d'un article</h2>

        <div>
            <?php
            require 'message.php';
            ?>

            <form action="?page=blog-admin" method="post">
                <input type="hidden" name="add-post">
                <p>
                    <input class="btn" type="submit" value="Publier l'article">
                </p>
                <p>
                    <label for="post-title">Titre de l'article</label><br>
                    <input class="admin-input" type="text" name="post-title" id="post-title">
                </p>
                <p>
                    <label for="post-excerpt">Extrait de l'article (ou chapo)</label><br>
                    <textarea class="admin-textarea" name="post-excerpt" id="post-excerpt" cols="30" rows="5"></textarea>
                </p>
                <p>
                    <label for="post-content">Contenu de l'article</label><br>
                    <textarea class="admin-textarea" name="post-content" id="post-content" cols="30" rows="30"></textarea>
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