<!DOCTYPE html>
<html>
<head>
  <?php $event->trigger('head') ?>
  <meta charset="utf-8"/>
  <meta name="description" content=""/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $setting('site.title') ?></title>
  <link rel="stylesheet" href="<?= $asset([
    'comps/bootstrap-custom/css/bootstrap.min.css',
    'comps/bootstrap-mobile/dist/css/bootstrap-mobile.css',
    'assets/tips.css',
    'assets/swipe.css',
    'plugins/app/css/app.css',
  ]) ?>">
  <?= $block->get('css') ?>
  <script src="//cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
  <script src="<?= $asset([
    'comps/requirejs/require.js',
    'assets/require.js',
    'comps/bootstrap/dist/js/bootstrap.min.js',
    'assets/app.js',
  ]) ?>"></script>
  <script>
    $.extend($, <?= json_encode($app->getConfig()) ?>);
  </script>
  <?php $event->trigger('appendHead') ?>
</head>
<body>

<?= $content ?>

</body>
</html>
