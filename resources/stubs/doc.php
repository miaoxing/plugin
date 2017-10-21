<?php

echo '<?php';
?>


namespace <?= $namespace ?> {

    /**
<?= $docBlock ?>
     */
    class <?= $class, "\n" ?>
    {
    }
}

namespace {

    /**
     * @return <?= $namespace ?>\<?= $class, "\n" ?>
     */
    function wei()
    {
    }
}
