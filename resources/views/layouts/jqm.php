<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e($wei->page->getTitle()) ?></title>
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.0/jquery.mobile.min.css">
  <link rel="stylesheet" href="<?= $asset('plugins/app/css/jqm.css') ?>">
  <?= $wei->page->renderHead() ?>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.0/jquery.mobile.min.js"></script>
  <script src="<?= $asset('comps/requirejs/require.min.js') ?>"></script>
  <script src="<?= $asset('plugins/app/js/require-config.js') ?>"></script>
  <script src="<?= $asset('plugins/app/js/app.js') ?>"></script>
  <script src="<?= $asset('assets/popup.js') ?>"></script>
  <script src="<?= $asset('assets/jqueryMobile.js') ?>"></script>
  <script>
    $.extend($, <?= json_encode($app->getConfig()) ?>);
  </script>
</head>
<body>
<?php $event->trigger('bodyStart', [$headerTitle]) ?>

<?= $content ?>
<?= $block->get('html') ?>

<?= $wei->page->renderFooter() ?>
</body>
</html>
