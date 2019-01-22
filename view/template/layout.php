<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title><?= $pageTitle ?></title>
        <link href="public/css/style.css" rel="stylesheet" />
    </head>

    <body>
        <header>
            <h1>Blog</h1>
            <?= $header ?>
        </header>

        <section>
            <?= $content ?>
        </section>

        <footer>
            <?= $footer ?>
        </footer>
    </body>
</html>