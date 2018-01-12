<!DOCTYPE html>
<html>
<head>
  <?php $event->trigger('head') ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <?php $event->trigger('prependHead') ?>
  <title><?= $e(isset($htmlTitle) ? $htmlTitle : $setting('site.title')) ?></title>
  <link rel="stylesheet" href="<?= $asset([
    'comps/bootstrap-custom/css/bootstrap.min.css',
    'comps/bootstrap-mobile/dist/css/bootstrap-mobile.css',
    'plugins/app/css/tips.css',
    'plugins/app/css/swipe.css',
    'plugins/app/css/app.css',
  ]) ?>">
  <?php require $view->getFile('@plugin/layouts/default-css.php') ?>
  <?php $event->trigger('style') ?>
  <?= $block->get('css') ?>
  <?php $event->trigger('appendHead') ?>
</head>
<body class="<?= isset($bodyClass) ? $bodyClass : '' ?>">

<div id="<?= $pageId ?>">
  <?php $event->trigger('beforeContent', [$pageConfig, isset($menuTitle) ? $menuTitle : $headerTitle]) ?>
  <?= $content ?>
  <?php $event->trigger('afterContent') ?>
</div>

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
<?= $block->get('html') ?>
<?php $event->trigger('script') ?>
<?= $block->get('js') ?>
<?php $event->trigger('afterScript') ?>
</body>
</html>
