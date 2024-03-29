<?php

namespace Miaoxing\Plugin\Service;

use Wei\BaseModel;
use Wei\Req;
use Wei\Res;

/**
 * @OA\Schema(
 *   schema="Ret",
 *   required={"code", "message"}
 * )
 * @OA\Property(
 *   property="code",
 *   type="integer",
 *   description="返回编号"
 * )
 * @OA\Property(
 *   property="message",
 *   type="string",
 *   description="返回信息"
 * )
 * @mixin \PluginMixin
 * @mixin \EnvMixin
 * @mixin \ReqMixin
 * @mixin \ResMixin
 * @mixin \ViewMixin
 */
class Ret extends \Wei\Ret
{
    /**
     * @var array
     * @internal
     */
    protected static $errors = [];

    /**
     * Convert Ret object to response
     *
     * @param Req|null $req
     * @param Res|null $res
     * @return Res
     * @throws \Exception
     */
    public function toRes(?Req $req = null, ?Res $res = null)
    {
        $req || $req = $this->req;
        $res || $res = $this->res;

        $model = $this->getMetadata('model');
        if ($model instanceof BaseModel && $model->wasRecentlyCreated()) {
            $res->setStatusCode(201);
        }

        if ($req->acceptJson() || $this->isApi($req)) {
            return $res->json($this);
        } else {
            $type = $this->data['retType'] ?? ($this->isSuc() ? 'success' : 'warning');
            $content = $this->view->render('@plugin/_ret.php', $this->data + ['type' => $type]);
            return $res->setContent($content);
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     * @svc
     */
    protected function err($message, $code = null, $level = null)
    {
        if (null === $code && !isset($message['code']) && $this->env->isDev()) {
            $code = $this->generateCode($message) ?? $this->defaultErrCode;
        }
        return parent::err($message, $code, $level);
    }

    /**
     * @param string|array $message
     * @return int|null
     * @throws \Exception
     * @internal
     */
    protected function generateCode($message)
    {
        $traces = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 4);

        if ('err' === $traces[3]['function']) {
            // err();
            $file = $traces[3]['file'];
        } else {
            // Ret:err();
            $file = $traces[2]['file'];
        }

        $name = $this->getPluginFromFile($file);
        if (!$name) {
            return null;
        }

        $plugin = $this->plugin->getOneById($name);
        if (!$plugin->getCode()) {
            return null;
        }

        $errors = $this->getErrors($name);
        $key = is_array($message) ? ($message['message'] ?? $message[0]) : $message;
        if (isset($errors[$key])) {
            $code = $errors[$key];
        } else {
            $code = (end($errors) ?: $plugin->getCode() * 1000) + 1;
            $errors[$key] = $code;
            $this->setErrors($name, $errors);
        }

        return $code;
    }

    /**
     * @param string $file
     * @return string|null
     * @internal
     */
    protected function getPluginFromFile(string $file): ?string
    {
        $parts = explode('/plugins/', $file);
        if (1 === count($parts)) {
            // Not in plugin
            return null;
        }
        return explode('/', $parts[1], 2)[0];
    }

    /**
     * @param string $name
     * @return array
     * @internal
     */
    protected function getErrors(string $name): array
    {
        if (!isset(static::$errors[$name])) {
            $file = $this->getErrorFile($name);
            if (is_file($file)) {
                static::$errors[$name] = require $file;
            } else {
                static::$errors[$name] = [];
            }
        }
        return static::$errors[$name];
    }

    /**
     * @param string $name
     * @param array $errors
     * @internal
     */
    protected function setErrors(string $name, array $errors): void
    {
        static::$errors[$name] = $errors;
        $file = $this->getErrorFile($name);
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        // Convert to short array syntax
        $content = "<?php\n\nreturn [" . substr(var_export($errors, true), strlen('array ('), -1) . "];\n";
        file_put_contents($this->getErrorFile($name), $content);
    }

    /**
     * @param string $name
     * @return string
     * @internal
     */
    protected function getErrorFile(string $name): string
    {
        return 'plugins/' . $name . '/config/errors.php';
    }

    /**
     * 判断是否为API接口
     *
     * @param Req $req
     * @return bool
     */
    private function isApi(Req $req)
    {
        $pathInfo = $req->getRouterPathInfo();
        $path = explode('/', $pathInfo, 3)[1];
        if ('api' === $path || '-api' === substr($path, -4)) {
            return true;
        }
        return false;
    }
}
