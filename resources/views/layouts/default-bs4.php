<?php

$wei->page->addPluginAssets('app-bs4');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e($wei->page->getTitle()) ?></title>
  <?= $wei->page->renderHead() ?>
</head>
<body>
<?php $event->trigger('bodyStart') ?>
<?= $content ?>
<div id="root"></div>
<script>
  var wei = <?= json_encode($js) ?>;
</script>
<?= $wei->page->renderFooter() ?>
</body>
</html>
