<?php

namespace Miaoxing\Plugin\PHPStan\Reflection;

use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\ObjectType;

class ServicePropertyReflection implements PropertyReflection
{
    protected $type;
    protected $classReflection;

    public function __construct($class, $classReflection)
    {
        $this->type = new ObjectType($class);
        $this->classReflection = $classReflection;
    }

    public function getDeclaringClass(): \PHPStan\Reflection\ClassReflection
    {
        return $this->classReflection;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getDocComment(): ?string
    {
        return null;
    }

    public function getReadableType(): \PHPStan\Type\Type
    {
        return $this->type;
    }

    public function getWritableType(): \PHPStan\Type\Type
    {
        return $this->type;
    }

    public function canChangeTypeAfterAssignment(): bool
    {
        return false;
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function isDeprecated(): \PHPStan\TrinaryLogic
    {
        return \PHPStan\TrinaryLogic::createNo();
    }

    public function getDeprecatedDescription(): ?string
    {
        return null;
    }

    public function isInternal(): \PHPStan\TrinaryLogic
    {
        return \PHPStan\TrinaryLogic::createNo();
    }
}