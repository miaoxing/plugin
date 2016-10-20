<?php

namespace Miaoxing\Plugin\Middleware;

class IpRestrict extends Base
{
    protected $allowedIps;

    public function __invoke($next)
    {
        if (!$this->allowedIps) {
            return $this->response
                ->setStatusCode(403)
                ->setContent(sprintf('Allowed IPs is not set!'));
        }

        $ip = $this->request->getIp();
        if (!in_array($ip, $this->allowedIps)) {
            return $this->response
                ->setStatusCode(403)
                ->setContent(sprintf('IP %s is not allowed!', $ip));
        }

        return $next();
    }
}
