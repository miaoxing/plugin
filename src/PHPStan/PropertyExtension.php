<?php

namespace Miaoxing\Plugin\PHPStan;

use Miaoxing\Plugin\PHPStan\Reflection\ServicePropertyReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use Wei\Wei;

/**
 * 允许通过 wei()->xxx 调用服务
 *
 * 对应错误：
 *  "#^Access to an undefined property Wei\\\\Wei\\:\\:\\$(.+?)\\.$#"
 */
class PropertyExtension implements PropertiesClassReflectionExtension
{
    public function __construct()
    {
        $alieses = wei()->classMap->generate(['src', 'plugins/*/src'], '/Service/*.php', 'Service');
        wei()->setAliases($alieses);
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        return $classReflection->getName() === Wei::class
            && !property_exists(Wei::class, $propertyName)
            && \wei()->has($propertyName);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        $class = wei()->getClass($propertyName);
        return new ServicePropertyReflection($class, $classReflection);
    }
}