<?php

namespace MiaoxingTest\Plugin\Doc {

    use MiaoxingTest\Plugin\Fixture\TestDefaultScope;
    use MiaoxingTest\Plugin\Fixture\TestSoftDelete;

    /**
     * @method TestSoftDelete|TestSoftDelete[] testSoftDelete()
     * @method TestDefaultScope|TestDefaultScope[] testDefaultScope()
     */
    class AutoComplete
    {
    }
}

namespace {

    /**
     * @return MiaoxingTest\Plugin\Doc\AutoComplete
     */
    function wei()
    {
    }
}
