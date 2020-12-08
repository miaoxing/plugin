<?= '<?php', "\n" ?>

namespace <?= $namespace ?>;

use Miaoxing\Plugin\Model\ModelTrait;

/**
<?= $docBlock ?>
 * @internal will change in the future
 */
trait <?= $class, "\n" ?>
{
    use ModelTrait;

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
