<?php

$wei->page->addPluginAssets('app', true);
?>
<!DOCTYPE html>
<html<?= $wei->ua->isIOS() ? ' class="ios"' : '' ?>>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e($wei->page->getTitle()) ?></title>
  <?= $wei->page->renderHead() ?>
  <?php require $view->getFile('@plugin/layouts/default-css.php') ?>
</head>
<body>
<?php $event->trigger('bodyStart') ?>

<?= $content ?>
<div id="root"></div>
<?= $block->get('html') ?>

<script>
  var wei = <?= json_encode($js) ?>;
</script>
<?= $wei->page->renderFooter() ?>
<script>
  $.extend($, <?= json_encode($app->getConfig()) ?>);
</script>
</body>
</html>
