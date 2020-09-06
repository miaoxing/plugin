<?php

declare(strict_types=1);

namespace Miaoxing\Plugin\Service;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Miaoxing\Plugin\BaseService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

/**
 * @mixin \ReqMixin
 */
class Jwt extends BaseService
{
    public const CODE_EXPIRED = 200007;

    /**
     * @var string
     */
    protected $signerClass = Sha256::class;

    /**
     * @var string
     */
    protected $privateKey = 'file://storage/cert/private.key';

    /**
     * @var string
     */
    protected $publicKey = 'file://storage/cert/public.key';

    /**
     * @param array $claims
     * @param int $expire
     * @return Token
     * @throws \Exception
     * @svc
     */
    protected function generate(array $claims, int $expire = 86400 * 30): Token
    {
        $time = time();
        $builder = (new Builder())->issuedBy($this->req->getSchemeAndHost())
            ->identifiedBy(base64_encode(random_bytes(8)), true)
            ->issuedAt($time)
            ->expiresAt($time + $expire);
        foreach ($claims as $name => $value) {
            $builder->withClaim($name, $value);
        }
        return $builder->getToken($this->getSigner(), new Key($this->getPrivateKey()));
    }

    /**
     * @param string $token
     * @return Ret
     * @svc
     */
    protected function verify(string $token): Ret
    {
        if (!$token) {
            return err('Token 不能为空');
        }

        if (strpos($token, ' ') !== false) {
            $token = explode(' ', $token)[1];
        }

        try {
            $parsedToken = (new Parser())->parse($token);
        } catch (\Throwable $e) {
            return err([
                'message' => '解析 Token 失败',
                'detail' => $e->getMessage(),
            ]);
        }

        if ($parsedToken->isExpired()) {
            return err('Token 已过期', static::CODE_EXPIRED);
        }

        if (!$parsedToken->verify($this->getSigner(), $this->getPublicKey())) {
            return err('Token 签名错误');
        }

        $data = new ValidationData();
        $data->setIssuer($this->req->getSchemeAndHost());
        if (!$parsedToken->validate($data)) {
            return err('Token 内容错误');
        }

        return suc(['token' => $parsedToken]);
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey ?: $this->privateKey;
    }

    public function getSigner(): Signer
    {
        return new $this->signerClass;
    }
}
