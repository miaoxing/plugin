<?php

namespace Miaoxing\Plugin\PHPStan\Reflection;

use PHPStan\Reflection\Php\PhpMethodReflection;

/**
 * Allow `Static call to instance method`
 */
class StaticMethodReflection implements \PHPStan\Reflection\MethodReflection
{
    protected $method;

    public function __construct(PhpMethodReflection $method)
    {
        $this->method = $method;
    }

    public function isStatic(): bool
    {
        return true;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getDeclaringClass(): \PHPStan\Reflection\ClassReflection
    {
        return $this->method->getDeclaringClass();
    }

    public function isPrivate(): bool
    {
        return $this->method->isPrivate();
    }

    public function getDocComment(): ?string
    {
        return $this->method->getDocComment();
    }

    public function getName(): string
    {
        return $this->method->getName();
    }

    public function getPrototype(): \PHPStan\Reflection\ClassMemberReflection
    {
        return $this->method->getPrototype();
    }

    public function getVariants(): array
    {
        return $this->method->getVariants();
    }

    public function isDeprecated(): \PHPStan\TrinaryLogic
    {
        return $this->method->isDeprecated();
    }

    public function getDeprecatedDescription(): ?string
    {
        return $this->method->getDeprecatedDescription();
    }

    public function isFinal(): \PHPStan\TrinaryLogic
    {
        return $this->method->isFinal();
    }

    public function isInternal(): \PHPStan\TrinaryLogic
    {
        return $this->method->isInternal();
    }

    public function getThrowType(): ?\PHPStan\Type\Type
    {
        return $this->method->getThrowType();
    }

    public function hasSideEffects(): \PHPStan\TrinaryLogic
    {
        return $this->method->hasSideEffects();
    }
}