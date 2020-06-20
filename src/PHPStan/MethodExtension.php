<?php

namespace Miaoxing\Plugin\PHPStan;

use Miaoxing\Plugin\PHPStan\Reflection\InvokeMethodReflection;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\UnionType;
use Wei\Wei;

/**
 * 允许通过 wei()->xxx() 调用服务
 *
 * 对应错误：
 * "#^Call to an undefined method Wei\\\\Wei\\:\\:(.+?)\\(\\)\\.$#"
 */
class MethodExtension implements \PHPStan\Reflection\MethodsClassReflectionExtension, BrokerAwareExtension
{
    /**
     * @var Broker
     */
    protected $broker;

    public function __construct()
    {
        $alieses = wei()->classMap->generate(['src', 'plugins/*/src'], '/Service/*.php', 'Service');
        wei()->setAliases($alieses);
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return $classReflection->getName() === Wei::class
            && !method_exists(Wei::class, $methodName)
            && \wei()->has($methodName);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $class = wei()->getClass($methodName);
        $methodReflection = $this->broker->getClass($class)->getMethod('__invoke', new OutOfClassScope());

        // 如果返回值包含了 $this|static, 会被认为是 Wei，因此替换返回为当前类
        // 报错参考： App.php:132 Cannot call method select() on array<Wei\Wei>|Wei\Wei.
        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());
        $returnType = $parametersAcceptor->getReturnType();
        $variants = $methodReflection->getVariants();

        $type = $this->replaceType($returnType, $class);
        $variant = $variants[0];
        $variants[0] = new FunctionVariant(
            $variant->getTemplateTypeMap(),
            $variant->getResolvedTemplateTypeMap(),
            $variant->getParameters(),
            $variant->isVariadic(),
            $type
        );

        return new InvokeMethodReflection($methodReflection, $methodName, $variants);
    }

    public function setBroker(Broker $broker): void
    {
        $this->broker = $broker;
    }

    private function replaceType(\PHPStan\Type\Type $type, $class)
    {
        // NOTE: ThisType 包含了 StaticType
        if ($type instanceof StaticType) {
            return new ObjectType($class);
        }

        if ($type instanceof ArrayType) {
            return new ArrayType($type->getKeyType(), $this->replaceType($type->getItemType(), $class));
        }

        if ($type instanceof UnionType) {
            $types = [];
            foreach ($type->getTypes() as $subType) {
                $types[] = $this->replaceType($subType, $class);
            }
            return new UnionType($types);
        }

        return $type;
    }
}