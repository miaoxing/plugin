<?php $view->layout() ?>

<?= $block->css() ?>
<style>
  <?= file_get_contents(__DIR__ . '/../css/ret.css') ?>
</style>
<?= $block->end() ?>

<div class="ret ret-<?= $type ?>">
  <div class="ret-icon-container">
    <i class="ret-icon ret-icon-<?= $type ?>"></i>
  </div>
  <h2 class="ret-title"><?= $message ?></h2>
  <!--<p class="ret-detail">详细提示</p>-->
</div>
