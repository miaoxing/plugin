<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $setting('site.title') ?></title>
  <link rel="stylesheet" href="<?= $asset([
    'comps/bootstrap-custom/css/bootstrap.min.css',
    'comps/bootstrap-mobile/dist/css/bootstrap-mobile.css',
    'plugins/app/css/tips.css',
    'plugins/app/css/swipe.css',
    'plugins/app/css/app.css',
  ]) ?>">
  <?= $block->get('css') ?>
  <script src="//cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
  <script src="<?= $asset([
    'comps/requirejs/require.js',
    'comps/bootstrap/dist/js/bootstrap.min.js',
    'plugins/app/js/app.js',
    'plugins/app/js/require-config.js',
  ]) ?>"></script>
  <script>
    $.extend($, <?= json_encode($app->getConfig()) ?>);
  </script>
</head>
<body>

<?= $content ?>

</body>
</html>
