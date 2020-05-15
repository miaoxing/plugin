<?php

namespace MiaoxingTest\Plugin\Fixture\Controller;

use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\Service\UserModel;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnResponse;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnRet;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnString;
use Wei\RetTrait;

class TestController extends BaseController
{
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

    public function returnCodeAndMessageAction()
    {
        $code = 1;
        $message = 'returnCodeAndMessage';

        return get_defined_vars();
    }

    public function returnOnlyCodeAction()
    {
        $code = 1;

        return get_defined_vars();
    }

    public function returnOnlyMessageAction()
    {
        $message = 'message';

        return get_defined_vars();
    }

    public function returnResponseAction()
    {
        return $this->response->setContent('returnResponse');
    }

    public function returnEmptyArrayWillRenderViewAction()
    {
        return get_defined_vars();
    }

    public function returnRetInMiddlewareAction()
    {
        throw new \Exception('test error');
    }

    public function returnStringInMiddlewareAction()
    {
        throw new \Exception('test error');
    }

    public function returnResponseInMiddlewareAction()
    {
        throw new \Exception('test error');
    }

    public function paramAction($id)
    {
        return $id;
    }

    public function paramWithTypeAction(int $id)
    {
        return gettype($id) . '-' . $id;
    }

    public function paramWithDefaultValueAction($id = 'test')
    {
        return $id;
    }

    public function paramWithTypeAndDefaultValueAction(bool $isEnabled = null)
    {
        return var_export($isEnabled, true);
    }

    public function paramModelAction(UserModel $user)
    {
        return 'user:' . $user->id;
    }
}
