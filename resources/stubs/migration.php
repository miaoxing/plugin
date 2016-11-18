<?php

echo '<?php';
?>

<?php if ($namespace) : ?>

namespace <?= $namespace ?>;
<?php endif ?>

use Miaoxing\Plugin\BaseMigration;

class <?= $class ?> extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        //
    }
}
