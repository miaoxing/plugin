<?php

namespace Miaoxing\Plugin\PHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Generic\TemplateTypeMap;

class InvokeMethodReflection implements \PHPStan\Reflection\MethodReflection
{
    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var MethodReflection
     */
    protected $methodReflection;

    /**
     * @var \PHPStan\Reflection\ClassReflection
     */
    protected $class;

    /**
     * @var array
     */
    protected $variants;

    public function __construct(MethodReflection $methodReflection, string $methodName, array $variants)
    {
        $this->methodReflection = $methodReflection;
        $this->methodName = $methodName;
        $this->variants = $variants;
    }

    public function getDeclaringClass(): \PHPStan\Reflection\ClassReflection
    {
        return $this->methodReflection->getDeclaringClass();
    }

    public function isStatic(): bool
    {
        return $this->methodReflection->isStatic();
    }

    public function isPrivate(): bool
    {
        return $this->methodReflection->isPrivate();
    }

    public function isPublic(): bool
    {
        return $this->methodReflection->isPublic();
    }

    public function getDocComment(): ?string
    {
        return $this->methodReflection->getDocComment();
    }

    public function getName(): string
    {
        return $this->methodName;
    }

    public function getPrototype(): \PHPStan\Reflection\ClassMemberReflection
    {
        return $this->methodReflection->getPrototype();
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function isDeprecated(): \PHPStan\TrinaryLogic
    {
        return $this->methodReflection->isDeprecated();
    }

    public function getDeprecatedDescription(): ?string
    {
        return $this->methodReflection->getDeprecatedDescription();
    }

    public function isFinal(): \PHPStan\TrinaryLogic
    {
        return $this->methodReflection->isFinal();
    }

    public function isInternal(): \PHPStan\TrinaryLogic
    {
        return $this->methodReflection->isInternal();
    }

    public function getThrowType(): ?\PHPStan\Type\Type
    {
        return $this->methodReflection->getThrowType();
    }

    public function hasSideEffects(): \PHPStan\TrinaryLogic
    {
        return $this->methodReflection->hasSideEffects();
    }
}