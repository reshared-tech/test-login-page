<!DOCTYPE html>
<html lang="en">
<head>
    <?= base_tag() ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __($title ?? 'Welcome') ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <?php if (isset($heads)): ?>
        <?php foreach ($heads as $head): ?>
            <?= $head ?>
        <?php endforeach ?>
    <?php endif ?>
</head>
<body>