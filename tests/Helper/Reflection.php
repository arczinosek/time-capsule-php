<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use ReflectionClass;

class Reflection
{
    public static function setObjectProperty(
        object $object,
        mixed $value,
        string $propertyName = 'id'
    ): object {
        $meta = new ReflectionClass($object);

        if ($meta->hasProperty($propertyName)) {
            $property = $meta->getProperty($propertyName);
            $property->setValue($object, $value);
        }

        return $object;
    }
}
