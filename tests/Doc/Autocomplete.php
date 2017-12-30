<?php

namespace MiaoxingTest\Plugin\Doc {

    use MiaoxingTest\Plugin\Fixture\TestArticle;
    use MiaoxingTest\Plugin\Fixture\TestCamelArticle;
    use MiaoxingTest\Plugin\Fixture\TestDefaultScope;
    use MiaoxingTest\Plugin\Fixture\TestSoftDelete;

    /**
     * @method TestArticle|TestArticle[] testArticle()
     * @method TestCamelArticle|TestCamelArticle[] testCamelArticle()
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
