<?php

namespace Miaoxing\Plugin\Middleware;

use Miaoxing\Services\Middleware\BaseMiddleware;

class CheckRedirectUrl extends BaseMiddleware
{
    /**
     * 白名单域名
     *
     * @var array
     * @todo middleware如何象service一样配置
     */
    protected $whitelist = [
        'slave.gz.1251323154.clb.myqcloud.com',
        's.bzy2015.com',
        'm.gz.1251323154.clb.myqcloud.com',
        'ht.bzy2015.com',
    ];

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        if ($nextUrl = $this->request->getQuery('next')) {
            $host = parse_url($nextUrl, PHP_URL_HOST);
            if ($host && $host !== $this->request->getHost() && !in_array($host, $this->whitelist)) {
                return $this->response->json([
                    'code' => -400,
                    'message' => 'Bad Request',
                ]);
            }
        }

        return $next();
    }
}
