<!DOCTYPE html>
<html>
<head>
  <?php $event->trigger('head') ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e(isset($htmlTitle) ? $htmlTitle : $setting('site.title')) ?></title>
  <link rel="stylesheet" href="<?= $asset([
    'comps/bootstrap-custom/css/bootstrap.min.css',
    'comps/bootstrap-mobile/dist/css/bootstrap-mobile.css',
    'plugins/app/css/tips.css',
    'plugins/app/css/swipe.css',
    'plugins/app/css/app.css',
  ]) ?>">
  <?php require $view->getFile('@plugin/layouts/default-css.php') ?>
  <?= $wei->layout->renderHead() ?>
</head>
<body class="<?= isset($bodyClass) ? $bodyClass : '' ?>">
<?php $event->trigger('bodyStart', [$headerTitle]) ?>

<?= $content ?>
<?= $block->get('html') ?>

<script src="<?= $mainJs = $asset([
  'comps/fastclick-custom/fastclick.js',
  'comps/requirejs/require.min.js',
  'comps/jquery/jquery.min.js',
  'comps/jquery-list/jquery-list.js',
  'comps/bootstrap-custom/js/bootstrap.min.js',
  'comps/bootstrap-mobile/dist/js/bootstrap-mobile.min.js',
  'comps/Swipe/swipe.js',
  'comps/jquery-lazy/jquery-lazy.js',
  'plugins/app/js/app.js',
  'plugins/app/js/require-config.js',
  'plugins/app/js/tips.js',
  'plugins/app/js/bootstrap-popup.js',
  'plugins/app/js/bootstrap-ajax-tips.js',
]) ?>"></script>
<script>window.requirejs || document.write('<script src="<?= $asset->fallback($mainJs) ?>"><\/script>')</script>
<script>
  $.extend($, <?= json_encode($app->getConfig()) ?>);
  var wei = <?= json_encode($js) ?>;
</script>
<?= $wei->layout->renderFooter() ?>
</body>
</html>
