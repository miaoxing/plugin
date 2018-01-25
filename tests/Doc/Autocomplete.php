<?php

namespace MiaoxingTest\Plugin\Doc {

    use MiaoxingTest\Plugin\Fixture\TestArticle;
    use MiaoxingTest\Plugin\Fixture\TestCamelCase;
    use MiaoxingTest\Plugin\Fixture\TestDefaultScope;
    use MiaoxingTest\Plugin\Fixture\TestCast;
    use MiaoxingTest\Plugin\Fixture\TestMutator;
    use MiaoxingTest\Plugin\Fixture\TestProfile;
    use MiaoxingTest\Plugin\Fixture\TestRef;
    use MiaoxingTest\Plugin\Fixture\TestSoftDelete;
    use MiaoxingTest\Plugin\Fixture\TestTag;
    use MiaoxingTest\Plugin\Fixture\TestUser;
    use MiaoxingTest\Plugin\Fixture\TestVirtual;

    /**
     * @method TestUser|TestUser[] testUser()
     * @method TestArticle|TestArticle[] testArticle()
     * @method TestProfile|TestProfile[] testProfile()
     * @method TestTag|TestTag[] testTag()
     * @method TestCamelCase|TestCamelCase[] testCamelCase()
     * @method TestSoftDelete|TestSoftDelete[] testSoftDelete()
     * @method TestDefaultScope|TestDefaultScope[] testDefaultScope()
     * @method TestCast|TestCast[] testCast()
     * @method TestMutator|TestMutator[] testMutator()
     * @method TestRef|TestRef[] testRef()
     * @method TestVirtual|TestVirtual[] testVirtual()
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
