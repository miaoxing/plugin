<!DOCTYPE html>
<html>
<head>
  <?php $event->trigger('head') ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <?php $event->trigger('prependHead') ?>
  <title><?= $setting('site.title') ?></title>
  <link rel="stylesheet" href="//cdn.bootcss.com/jquery-mobile/1.4.0/jquery.mobile.min.css">
  <link rel="stylesheet" href="<?= $asset([
    'assets/swipe.css',
    'vendor/miaoxing/app/assets/jqm.css'
  ]) ?>">
  <?php $event->trigger('prePageCss') ?>
  <?= $block->get('css') ?>
  <?php $event->trigger('postPageCss') ?>
  <script src="//cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
  <script src="//cdn.bootcss.com/jquery-mobile/1.4.0/jquery.mobile.min.js"></script>
  <script src="<?= $asset([
    'comps/requirejs/require.min.js',
    'assets/require.js',
    'comps/Swipe/swipe.js',
    'assets/app.js',
    'assets/popup.js',
    'assets/jqueryMobile.js'
  ]) ?>"></script>
  <script>
    $.extend($, <?= json_encode($app->getConfig()) ?>);
  </script>
  <?php $event->trigger('appendHead') ?>
</head>
<body>

<div data-role="page" id="<?= $pageId ?>">
  <?php $event->trigger('preContent', [$pageConfig, isset($menuTitle) ? $menuTitle : $headerTitle]) ?>
  <?= $content ?>
  <?php $event->trigger('postContent') ?>
</div>

<?php $event->trigger('inlineScript') ?>
<?php $event->trigger('beforePageScript') ?>
<?= $block->get('js') ?>
<?php $event->trigger('afterScript') ?>
</body>
</html>
