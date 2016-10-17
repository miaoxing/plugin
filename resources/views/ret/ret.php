<?php $view->layout() ?>

<link rel="stylesheet" href="<?= $asset('@plugin/css/ret.css') ?>">

<div class="ret ret-<?= $type ?>">
    <div class="ret-icon">
        <i class="icon-<?= $type ?>"></i>
    </div>
    <h2><?= $message ?></h2>
    <!--<p>详细提示</p>-->
</div>
