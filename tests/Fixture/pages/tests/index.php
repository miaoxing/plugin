<?php

use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\Service\UserModel;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnResponse;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnRet;
use MiaoxingTest\Plugin\Fixture\Middleware\ReturnString;
use Wei\RetTrait;

return new class extends BaseController {
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

    public function returnCodeAndMessage()
    {
        $code = 1;
        $message = 'returnCodeAndMessage';

        return get_defined_vars();
    }

    public function returnOnlyCode()
    {
        $code = 1;

        $this->app->setOption('defaultViewFile', 'tests/returnOnlyCode.php');
        return get_defined_vars();
    }

    public function returnOnlyMessage()
    {
        $message = 'message';

        $this->app->setOption('defaultViewFile', 'tests/returnOnlyMessage.php');
        return get_defined_vars();
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
        throw new \Exception('test error');
    }

    public function returnStringInMiddleware()
    {
        throw new \Exception('test error');
    }

    public function returnResponseInMiddleware()
    {
        throw new \Exception('test error');
    }

    public function param($id)
    {
        return $id;
    }

    public function paramWithType(int $id)
    {
        return gettype($id) . '-' . $id;
    }

    public function paramWithDefaultValue($id = 'test')
    {
        return $id;
    }

    public function paramWithTypeAndDefaultValue(bool $isEnabled = null)
    {
        return var_export($isEnabled, true);
    }

    public function paramModel(UserModel $user)
    {
        return 'user:' . $user->id;
    }
};
