<?php

namespace Miaoxing\Plugin\Middleware;

use Wei\RetTrait;

class IpRestrict extends Base
{
    use RetTrait;

    protected $allowedIps;

    public function __invoke($next)
    {
        if (!$this->allowedIps) {
            $this->response->setStatusCode(403);

            return $this->err('Allowed IPs is not set!');
        }

        $ip = $this->request->getIp();
        if (!in_array($ip, $this->allowedIps)) {
            $this->response->setStatusCode(403);

            return $this->err(sprintf('IP %s is not allowed!', $ip));
        }

        return $next();
    }
}
