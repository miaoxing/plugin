<?php

wei()->wpAsset->addRevFile('dist2/app-bs4-assets-hash.json');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e($wei->page->getTitle()) ?></title>
  <link rel="stylesheet" href="<?= $this->wpAsset('app-bs4.css') ?>"/>
  <?= $wei->page->renderHead() ?>
</head>
<body>
<?php $event->trigger('bodyStart') ?>

<?= $content ?>
<div id="root"></div>
<?= $block->get('html') ?>

<script src="<?= $this->wpAsset('app-bs4.js') ?>"></script>
<script>
  $.extend($, <?= json_encode($app->getConfig()) ?>);
</script>
<?= $wei->page->renderFooter() ?>
</body>
</html>
