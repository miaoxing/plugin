<?php echo '<?php', "\n" ?>

namespace <?php echo $namespace ?>;

use Miaoxing\Plugin\Queue\BaseJob;

class <?php echo $class ?> extends BaseJob
{
    /**
    * {@inheritdoc}
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * {@inheritdoc}
    */
    public function __invoke(): void
    {

    }
}
