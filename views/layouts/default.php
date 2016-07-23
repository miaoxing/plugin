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
    'assets/tips.css',
    'assets/swipe.css',
    'plugins/app/assets/app.css'
  ]) ?>">
  <?php $event->trigger('prePageCss') ?>
  <?= $block->get('css') ?>
  <?php $event->trigger('postPageCss') ?>
  <?php require $view->getFile('plugin:layouts/default-css.php') ?>
  <?php $event->trigger('appendHead') ?>
</head>
<body<?= isset($bodyClass) ? (' class="' . $bodyClass . '"') : '' ?>>

<div id="<?= $pageId ?>">
  <?php $event->trigger('preContent', [$pageConfig, isset($menuTitle) ? $menuTitle : $headerTitle]) ?>
  <?= $content ?>
  <?php $event->trigger('postContent') ?>
</div>

<script src="<?= $mainJs = $asset([
  'comps/fastclick-custom/fastclick.js',
  'comps/requirejs/require.min.js',
  'comps/jquery/jquery.min.js',
  'assets/require.js',
  'comps/Swipe/swipe.js',
  'assets/tips.js',
  'assets/app.js',
  'comps/jquery-list/jquery-list.js',
  'comps/bootstrap-custom/js/bootstrap.min.js',
  'comps/bootstrap-mobile/dist/js/bootstrap-mobile.min.js',
  'comps/jquery-lazy/jquery-lazy.js',
  'assets/bootstrapPopup.js',
  'assets/bootstrapAjaxTips.js'
]) ?>"></script>
<script>window.requirejs || document.write('<script src="<?= $asset->fallback($mainJs) ?>"><\/script>')</script>
<script>
  $.extend($, <?= json_encode($app->getConfig()) ?>);
</script>
<?php $event->trigger('inlineScript') ?>
<?php $event->trigger('beforePageScript') ?>
<?= $block->get('js') ?>
<?php $event->trigger('afterScript') ?>
</body>
</html>
