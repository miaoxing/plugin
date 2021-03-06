<?php

echo '<?php';
?>


namespace <?php echo $namespace ?> {

    /**
<?php echo $docBlock ?>
     */
    class <?php echo $class, "\n" ?>
    {
    }
}

namespace {

    /**
     * @return <?php echo $namespace ?>\<?php echo $class, "\n" ?>
     */
    function wei()
    {
    }

<?php echo $mixin ?>

<?php echo $viewVars ?>
}
