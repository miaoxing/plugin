<?php

use Miaoxing\Plugin\BasePage;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnResponse;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnRet;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnString;
use Wei\RetTrait;

return new /**
 * @mixin AppPropMixin
 */
class extends BasePage {
    use RetTrait;

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->middleware(ReturnRet::class, ['only' => 'returnRetInMiddleware']);
        $this->middleware(ReturnString::class, ['only' => 'returnStringInMiddleware']);
        $this->middleware(ReturnResponse::class, ['only' => 'returnResponseInMiddleware']);
    }

    public function sucAction()
    {
        return $this->suc();
    }

    public function errAction()
    {
        return $this->err('err', -2);
    }

    public function returnResponse()
    {
        return $this->res->setContent('returnResponse');
    }

    public function returnEmptyArrayWillRenderView()
    {
        $this->app->setOption('defaultViewFile', 'tests/returnEmptyArrayWillRenderView.php');
        return get_defined_vars();
    }

    public function returnRetInMiddleware()
    {
        throw new Exception('test error');
    }

    public function returnStringInMiddleware()
    {
        throw new Exception('test error');
    }

    public function returnResponseInMiddleware()
    {
        throw new Exception('test error');
    }
};
