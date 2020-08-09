<?= '<?php', "\n" ?>

namespace <?= $namespace ?>;

/**
 * <?= $class, "\n" ?>
 *
<?= $docBlock ?>
 * @internal will change in the future
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
