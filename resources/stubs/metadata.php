<?= '<?php', "\n" ?>

namespace <?= $namespace ?>;

/**
 * <?= $class, "\n" ?>
 *
<?= $docBlock ?>
 */
trait <?= $class, "\n" ?>
{
    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
<?php foreach ($casts as $column => $type) { ?>
        '<?= $column ?>' => '<?= $type ?>',
<?php } ?>
    ];
}
