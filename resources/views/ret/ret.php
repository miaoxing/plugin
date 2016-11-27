<?php $view->layout() ?>

<?= $block('css') ?>
<link rel="stylesheet" href="<?= $asset('plugins/plugin/css/ret.css') ?>">
<?= $block->end() ?>

<div class="ret ret-<?= $type ?>">
  <div class="ret-icon-container">
    <i class="ret-icon ret-icon-<?= $type ?>"></i>
  </div>
  <h2 class="ret-title"><?= $message ?></h2>
  <!--<p class="ret-detail">详细提示</p>-->
</div>
