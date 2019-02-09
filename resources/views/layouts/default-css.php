<?php // 成熟后独立为theme插件?>
<?php $color = $setting('theme.brandPrimary') ?: '#f28c48'; ?>
<!-- htmllint tag-bans="false" -->
<style>
  a, a:hover {
    color: <?= $color ?>;
  }
  .text-primary,
  .active.text-active-primary,
  .open > .text-active-primary,
  .active > .text-active-primary {
    color: <?= $color ?> !important;
  }
  .bg-primary {
    background-color: <?= $color ?> !important;
  }
  .btn-primary,
  .btn-primary:hover,
  .btn-primary:focus,
  .btn-primary.disabled,
  .btn-primary[disabled],
  .btn-primary:not(:disabled):not(.disabled):active,
  .btn-primary:not(:disabled):not(.disabled).active {
    background-color: <?= $color ?>;
    border-color: <?= $color ?>;
  }
  @media screen and (-webkit-min-device-pixel-ratio: 2) {
    .btn-primary.hairline::before {
      border-color: <?= $color ?>;
    }
  }
  .border-primary,
  .active.border-active-primary {
    border-color: <?= $color ?> !important;
  }
  .active.after-active-primary::after,
  .after-primary::after {
    color: <?= $color ?>;
    border-color: <?= $color ?>;
  }
</style>
<!-- htmllint tag-bans="$previous" -->
