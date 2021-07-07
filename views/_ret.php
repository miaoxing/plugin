<?php $view->layout() ?>

<?php echo $view->block->css() ?>
<style>
  <?php echo file_get_contents(__DIR__ . '/../css/ret.css') ?>
</style>
<?php echo $view->block->end() ?>

<div class="ret ret-<?php echo $type ?>">
  <div class="ret-icon-container">
    <i class="ret-icon ret-icon-<?php echo $type ?>"></i>
  </div>
  <h2 class="ret-title"><?php echo $message ?></h2>
  <!--<p class="ret-detail">详细提示</p>-->
</div>
