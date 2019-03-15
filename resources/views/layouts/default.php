<?php

$wei->page->addPluginAssets('app');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="format-detection" content="telephone=no">
  <title><?= $e($wei->page->getTitle()) ?></title>
  <?= $wei->page->renderHead() ?>
  <?php require $view->getFile('@plugin/layouts/default-css.php') ?>
</head>
<body>
<?php $event->trigger('bodyStart') ?>

<?= $content ?>
<div id="root"></div>
<?= $block->get('html') ?>

<script>
  var wei = <?= json_encode($js) ?>;
</script>
<?= $wei->page->renderFooter() ?>
<script>
  if (/iphone|ipod|ipad/i.test(navigator.appVersion)) {
    var event = function (e) {
      ['input', 'textarea', 'select'].includes(e.target.localName) && document.body.scrollIntoView(false)
    };
    $(document).on('show.bs.modal', function () {
      document.addEventListener('blur', event, true);
    });
    $(document).on('hide.bs.modal', function () {
      document.removeEventListener('blur', event, true);
    });
  }
</script>
</body>
</html>
