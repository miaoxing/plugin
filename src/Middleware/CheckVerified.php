<?php

namespace Miaoxing\Plugin\Middleware;

use Miaoxing\Plugin\Middleware\Base;
use Wei\RetTrait;

class CheckVerified extends Base
{
    use RetTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        if (wei()->curUser->isLogin() && !wei()->curUser['isValid']) {
            return $this->response->redirect(wei()->url('registration/confirm'));
        }

        return $next();
    }
}
