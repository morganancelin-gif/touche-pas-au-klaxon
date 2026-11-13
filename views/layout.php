<?php
/**
 * Layout commun à toutes les pages.
 *
 * @var string $content HTML de la page, produit par la vue.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Touche pas au klaxon</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main class="container flex-grow-1 my-4">
        <?= $content ?>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>

    <script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>