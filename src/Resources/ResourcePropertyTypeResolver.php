<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use BackedEnum;
use Mollie\Api\Traits\ComposableFromArray;
use Mollie\Api\Utils\Utility;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class ResourcePropertyTypeResolver
{
    /**
     * Per-class property-type map, keyed by resource class name.
     *
     * Each map entry describes one declared typed property:
     *   [
     *     'kind'       => 'scalar'|'enum'|'valueObject'|'mixed'|'unsupported',
     *     'scalar'     => 'string'|'int'|'bool'|'float'|null,
     *     'enums'      => class-string<BackedEnum>[] (for enum|string union),
     *     'valueObject'=> class-string|null (class with fromArray()),
     *     'allowsString' => bool  (for enum unions - keep raw string fallback),
     *     'nullable'   => bool,
     *   ]
     *
     * @var array<class-string, array<string, array<string, mixed>>>
     */
    private static array $propertyTypeCache = [];

    /**
     * Reflect (and cache) the typed properties of a resource class.
     *
     * @return array<string, array<string, mixed>>
     */
    public function typesFor(BaseResource $resource): array
    {
        $class = get_class($resource);

        if (isset(self::$propertyTypeCache[$class])) {
            return self::$propertyTypeCache[$class];
        }

        $map = [];

        foreach ((new \ReflectionClass($class))->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            if ($prop->isStatic()) {
                continue;
            }
            if (! $prop->hasType()) {
                continue;
            }

            $descriptor = $this->describeType($prop);
            if ($descriptor !== null) {
                $map[$prop->getName()] = $descriptor;
            }
        }

        return self::$propertyTypeCache[$class] = $map;
    }

    /**
     * Coerce a JSON value to match a declared property type descriptor.
     *
     * @param  array<string, mixed>  $descriptor
     * @param  mixed  $value
     * @return mixed
     */
    public function cast(array $descriptor, $value)
    {
        if ($value === null) {
            return $descriptor['nullable'] ? null : $value;
        }

        switch ($descriptor['kind']) {
            case 'scalar':
                return $this->coerceScalar($descriptor['scalar'], $value);

            case 'enum':
                foreach ($descriptor['enums'] as $enumClass) {
                    if (is_string($value) || is_int($value)) {
                        /** @var class-string<BackedEnum> $enumClass */
                        $case = $enumClass::tryFrom($value);
                        if ($case !== null) {
                            return $case;
                        }
                    }
                }

                return $descriptor['allowsString'] ? (is_scalar($value) ? (string) $value : $value) : $value;

            case 'valueObject':
                /** @var class-string $class */
                $class = $descriptor['valueObject'];

                if ($value instanceof $class) {
                    return $value;
                }

                if (is_object($value)) {
                    $value = (array) $value;
                }

                if (! is_array($value)) {
                    return $value;
                }

                return $class::fromArray($value);

            case 'mixed':
            default:
                return $value;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function describeType(ReflectionProperty $prop): ?array
    {
        $type = $prop->getType();

        if ($type === null) {
            return null;
        }

        $nullable = $type->allowsNull();
        $named = [];

        if ($type instanceof ReflectionNamedType) {
            $named[] = $type;
        } elseif ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $sub) {
                if ($sub instanceof ReflectionNamedType) {
                    $named[] = $sub;
                }
            }
        } else {
            return null;
        }

        $scalar = null;
        $allowsString = false;
        $enums = [];
        $valueObject = null;

        foreach ($named as $n) {
            $name = $n->getName();

            if ($n->isBuiltin()) {
                switch ($name) {
                    case 'string':
                        $allowsString = true;
                        $scalar = $scalar ?? 'string';

                        break;
                    case 'int':
                    case 'bool':
                    case 'float':
                        $scalar = $scalar ?? $name;

                        break;
                    case 'array':
                    case 'iterable':
                    case 'object':
                    case 'mixed':
                    case 'null':
                        return ['kind' => 'mixed', 'nullable' => $nullable];
                    default:
                        return ['kind' => 'mixed', 'nullable' => $nullable];
                }
            } else {
                if (is_subclass_of($name, BackedEnum::class)) {
                    $enums[] = $name;
                } elseif (class_exists($name) && $this->hasFromArray($name)) {
                    $valueObject = $valueObject ?? $name;
                } else {
                    return ['kind' => 'mixed', 'nullable' => $nullable];
                }
            }
        }

        if ($enums !== []) {
            return [
                'kind' => 'enum',
                'enums' => $enums,
                'allowsString' => $allowsString,
                'nullable' => $nullable,
            ];
        }

        if ($valueObject !== null) {
            return [
                'kind' => 'valueObject',
                'valueObject' => $valueObject,
                'nullable' => $nullable,
            ];
        }

        if ($scalar !== null) {
            return [
                'kind' => 'scalar',
                'scalar' => $scalar,
                'nullable' => $nullable,
            ];
        }

        return ['kind' => 'mixed', 'nullable' => $nullable];
    }

    private function hasFromArray(string $class): bool
    {
        if (! class_exists($class)) {
            return false;
        }

        if (method_exists($class, 'fromArray')) {
            return true;
        }

        return in_array(ComposableFromArray::class, Utility::classUsesRecursive($class), true);
    }

    /**
     * Coerce a JSON-decoded scalar to the declared PHP scalar type.
     *
     * @param  string  $target
     * @param  mixed  $value
     * @return mixed
     */
    private function coerceScalar(string $target, $value)
    {
        if ($target === 'string') {
            if (is_string($value)) {
                return $value;
            }
            if (is_int($value) || is_float($value)) {
                return (string) $value;
            }
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }

            return $value;
        }

        if ($target === 'int') {
            if (is_int($value)) {
                return $value;
            }
            if (is_string($value) && is_numeric($value)) {
                return (int) $value;
            }
            if (is_float($value)) {
                return (int) $value;
            }

            return $value;
        }

        if ($target === 'float') {
            if (is_float($value)) {
                return $value;
            }
            if (is_int($value)) {
                return (float) $value;
            }
            if (is_string($value) && is_numeric($value)) {
                return (float) $value;
            }

            return $value;
        }

        if ($target === 'bool') {
            if (is_bool($value)) {
                return $value;
            }

            return (bool) $value;
        }

        return $value;
    }
}
