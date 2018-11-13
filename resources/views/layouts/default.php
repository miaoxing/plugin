<?php

wei()->wpAsset->addRevFile('dist2/app-v1-assets-hash.json');
?>
<!DOCTYPE html>
<html<?= $wei->ua->isIOS() ? ' class="ios"' : '' ?>>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e($wei->page->getTitle()) ?></title>
  <link rel="stylesheet" href="<?= $this->wpAsset('app-v1.css') ?>"/>
  <?php require $view->getFile('@plugin/layouts/default-css.php') ?>
  <?= $wei->page->renderHead() ?>
</head>
<body>
<?php $event->trigger('bodyStart') ?>

<?= $content ?>
<div id="root"></div>
<?= $block->get('html') ?>

<script src="<?= $this->wpAsset('app-v1.js') ?>"></script>
<script>
  $.extend($, <?= json_encode($app->getConfig()) ?>);
  var wei = <?= json_encode($js) ?>;
</script>
<?= $wei->page->renderFooter() ?>
</body>
</html>
