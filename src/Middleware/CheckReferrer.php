<?php

namespace Miaoxing\Plugin\Middleware;

/**
 * 检查来源是否为当前域名或在白名单中
 */
class CheckReferrer extends Base
{
    /**
     * 白名单域名
     *
     * @var array
     */
    protected $whitelist = [
        'mp.weixin.qq.com',
    ];

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        if ($referrer = $this->request->getReferer()) {
            $host = parse_url($referrer, PHP_URL_HOST);
            if ($host !== $this->request->getHost() && !in_array($host, $this->whitelist)) {
                return $this->response->json([
                    'code' => -2000,
                    'message' => '来源不正确',
                ]);
            }
        }

        return $next();
    }
}
