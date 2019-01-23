<?php
$pageTitle = 'Gestion des articles';

// Header
ob_start();
require 'adminHeader.php';
$header = ob_get_clean();

// Content
ob_start();
?>
    <section class="blog-admin">
        <h2>Articles</h2>

        <p>
            <a class="btn" href="?page=post-editor">Ajouter un article</a>
        </p>

        <div>
            <?php
            require 'message.php';
            ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Etiquettes</th>
                    <th>Commentaires</th>
                    <th>Date de création</th>
                </tr>
            </thead>

            <tbody>
            <?php
            foreach ($posts as $post) {
                ?>
                <tr>
                    <td>
                        <form action="?page=post-editor" method="post">
                            <input type="hidden" name="post-id" value="<?= $post->getId() ?>">
                            <input class="admin-blogpost-title" type="submit" value="<?= $post->getTitle() ?>">
                        </form>
                    </td>
                    <td>
                        coming soon...
                    </td>
                    <td>
                        coming soon...
                    </td>
                    <td>
                        coming soon...
                    </td>
                    <td>
                        <?= $post->getCreationDate() ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>

            <tfoot>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Etiquettes</th>
                    <th>Commentaires</th>
                    <th>Date de création</th>
                </tr>
            </tfoot>
        </table>
    </section>
<?php

$content = ob_get_clean();

// Footer
ob_start();
require 'adminFooter.php';
$footer = ob_get_clean();

require 'main.php';