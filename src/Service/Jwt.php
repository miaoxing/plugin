<?php

declare(strict_types=1);

namespace Miaoxing\Plugin\Service;

use Ahc\Jwt\JWT as AJtw;
use Ahc\Jwt\JWTException;
use Miaoxing\Plugin\BaseService;
use ReqMixin;

/**
 * @mixin ReqMixin
 */
class Jwt extends BaseService
{
    public const CODE_EXPIRED = 200007;

    /**
     * @var string
     */
    protected $algo = 'RS256';

    /**
     * @var string
     */
    protected $privateKey = 'storage/keys/private.key';

    /**
     * @var string
     */
    protected $publicKey = 'storage/keys/public.key';

    /**
     * @var array
     */
    protected $messages = [
        AJtw::ERROR_TOKEN_INVALID => '解析 Token 失败',
        AJtw::ERROR_SIGNATURE_FAILED => 'Token 签名错误',
        AJtw::ERROR_TOKEN_EXPIRED => [
            'code' => self::CODE_EXPIRED,
            'message' => 'Token 已过期',
        ],
    ];

    /**
     * @return string
     * @svc
     */
    protected function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     * @return $this
     * @svc
     */
    protected function setPrivateKey(string $privateKey): self
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * @return string
     * @svc
     */
    protected function getPublicKey(): string
    {
        return $this->publicKey ?: $this->privateKey;
    }

    /**
     * @param string $publicKey
     * @return $this
     * @svc
     */
    protected function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * @param array $claims
     * @param int $expire
     * @return string
     * @throws \Exception
     * @svc
     */
    protected function generate(array $claims, int $expire = 86400 * 30): string
    {
        $jwt = new AJtw($this->getPrivateKey(), $this->algo);

        return $jwt->encode(array_merge([
            'iss' => $this->req->getSchemeAndHost(),
            'iat' => time(),
            'exp' => time() + $expire,
        ], $claims), [
            'jti' => base64_encode(random_bytes(8)),
        ]);
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

        $jwt = new AJtw($this->getPrivateKey(), $this->algo);

        try {
            $payload = $jwt->decode($token);
        } catch (JWTException $e) {
            $message = $this->messages[$e->getCode()] ?? $e->getMessage();
            return err($message);
        }

        if (isset($payload['iss']) && $payload['iss'] !== $this->req->getSchemeAndHost()) {
            return err('Token 内容错误');
        }

        return suc(['data' => $payload]);
    }

    /**
     * 生成默认配置所需的密钥
     *
     * @svc
     * @experimental
     */
    protected function generateDefaultKeys(): Ret
    {
        $res = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => \OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($res, $privateKey);

        $details = openssl_pkey_get_details($res);
        $publicKey = $details['key'];

        openssl_free_key($res);

        $ret = $this->write($this->publicKey, $publicKey);
        if ($ret->isErr()) {
            return $ret;
        }
        return $this->write($this->privateKey, $privateKey);
    }

    protected function write($path, $content): Ret
    {
        $dir = dirname($path);
        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            return err(['创建目录 %s 失败，请检查是否有权限操作', $dir]);
        }

        file_put_contents($path, $content);
        return suc();
    }
}
