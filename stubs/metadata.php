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
}
