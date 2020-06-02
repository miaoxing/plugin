<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e($wei->page->getTitle()) ?></title>
  <?= $wei->page->renderHead() ?>
</head>
<body>
<?php $event->trigger('bodyStart') ?>

<?= $content ?: '<div id="root"></div>' ?>
<?= $block->get('html') ?>

<?= $wei->page->renderFooter() ?>
</body>
</html>
