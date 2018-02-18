<!DOCTYPE html>
<html>
<head>
  <?php $event->trigger('head') ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $setting('site.title') ?></title>
  <link rel="stylesheet" href="//cdn.bootcss.com/jquery-mobile/1.4.0/jquery.mobile.min.css">
  <link rel="stylesheet" href="<?= $asset([
    'plugins/app/css/swipe.css',
    'plugins/app/css/jqm.css',
  ]) ?>">
  <?= $wei->layout->renderHead() ?>
  <script src="//cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
  <script src="//cdn.bootcss.com/jquery-mobile/1.4.0/jquery.mobile.min.js"></script>
  <script src="<?= $asset([
    'comps/requirejs/require.min.js',
    'plugins/app/js/require-config.js',
    'comps/Swipe/swipe.js',
    'plugins/app/js/app.js',
    'assets/popup.js',
    'assets/jqueryMobile.js',
  ]) ?>"></script>
  <script>
    $.extend($, <?= json_encode($app->getConfig()) ?>);
  </script>
</head>
<body>
<?php $event->trigger('bodyStart', [$headerTitle]) ?>

<?= $content ?>

<?= $wei->layout->renderFooter() ?>
</body>
</html>
