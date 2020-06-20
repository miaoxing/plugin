<?php

namespace Miaoxing\Plugin\PHPStan;

use Miaoxing\Plugin\PHPStan\Reflection\StaticMethodReflection;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Wei\Base;

/**
 * 允许服务静态，动态调用保护方法
 *
 * 对应错误：
 * "#^Static call to instance method#"
 * "#^Call to protected method (.+?) of class (.+?).$#"
 */
class ServiceExtension implements \PHPStan\Reflection\MethodsClassReflectionExtension
{
    protected $methods = [];

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (!$classReflection->isSubclassOf(Base::class)) {
            return false;
        }

        // Ignore not exists method, eg: wei()->notExists
        if (!method_exists($classReflection->getName(), $methodName)) {
            return false;
        }

        // 调用服务方法（不管是静态还是动态调用）
        $reflectionMethod = new \ReflectionMethod($classReflection->getName(), $methodName);
        if ($reflectionMethod->isProtected() && $this->isApi($reflectionMethod)) {
            return true;
        }

        return false;
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        /** @var PhpMethodReflection|StaticMethodReflection $method */
        $method = $classReflection->getMethod($methodName, new OutOfClassScope());
        if ($method instanceof StaticMethodReflection) {
            return $method;
        }

        $key = $classReflection->getCacheKey() . '.' . $methodName;
        if (!isset($this->methods[$key])) {
            $this->methods[$key] = new StaticMethodReflection($method);
        }
        return $this->methods[$key];
    }

    private function isApi(\ReflectionMethod $method)
    {
        return strpos($method->getDocComment(), '* @svc');
    }
}