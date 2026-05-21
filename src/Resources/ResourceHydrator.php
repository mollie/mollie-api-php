<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use BackedEnum;
use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;
use Mollie\Api\Http\Response;
use Mollie\Api\Traits\ComposableFromArray;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class ResourceHydrator
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
     *     'allowsString' => bool  (for enum unions — keep raw string fallback),
     *     'nullable'   => bool,
     *   ]
     *
     * @var array<class-string, array<string, array<string, mixed>>>
     */
    private static array $propertyTypeCache = [];

    /**
     * Hydrate a response into a resource or collection
     *
     * @param  object|array  $data
     * @return Response|BaseResource|BaseCollection|LazyCollection|IsWrapper
     */
    public function hydrate(BaseResource $resource, $data, Response $response)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        if ($resource instanceof AnyResource) {
            $resource->fill($data);
            $resource->setResponse($response);

            return $resource;
        }

        $typeMap = $this->reflectTypes($resource);

        foreach ($data as $property => $value) {
            $property = (string) $property;

            if ($this->holdsEmbeddedResources($resource, $property, $value)) {
                $resource->{$property} = $this->parseEmbeddedResources(
                    $resource->getConnector(),
                    $resource,
                    $value,
                    $response
                );

                continue;
            }

            if (isset($typeMap[$property])) {
                $resource->{$property} = $this->castValue($typeMap[$property], $value);

                continue;
            }

            // Tier 2: undeclared field → dynamic property
            $resource->{$property} = $value;
        }

        $resource->setResponse($response);

        return $resource;
    }

    /**
     * Hydrate a collection with data.
     *
     * @param  array|object  $items
     * @param  object|null  $_links
     */
    public function hydrateCollection(
        ResourceCollection $collection,
        $items,
        Response $response,
        $_links = null
    ): ResourceCollection {
        if (is_object($items)) {
            $items = (array) $items;
        }

        $hydratedItems = array_map(
            fn ($item) => $this->hydrate(
                ResourceFactory::create($response->getConnector(), $collection::getResourceClass()),
                $item,
                $response
            ),
            $items
        );

        if ($_links !== null) {
            $collection->_links = $_links;
        }

        return $collection
            ->setItems($hydratedItems)
            ->setResponse($response);
    }

    /**
     * Reflect (and cache) the typed properties of a resource class.
     *
     * @return array<string, array<string, mixed>>
     */
    private function reflectTypes(BaseResource $resource): array
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
                        // treat as free-form — keep as mixed
                        return ['kind' => 'mixed', 'nullable' => $nullable];
                    default:
                        // unknown builtin
                        return ['kind' => 'mixed', 'nullable' => $nullable];
                }
            } else {
                // Class-like type
                if (is_subclass_of($name, BackedEnum::class)) {
                    $enums[] = $name;
                } elseif (class_exists($name) && $this->hasFromArray($name)) {
                    $valueObject = $valueObject ?? $name;
                } else {
                    // stdClass, unknown class — pass through
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

        $traits = $this->classTraits($class);

        return in_array(ComposableFromArray::class, $traits, true);
    }

    /**
     * Resolve all traits used by a class hierarchy.
     *
     * @return list<string>
     */
    private function classTraits(string $class): array
    {
        $traits = [];

        $c = $class;
        while ($c !== false) {
            $traits = array_merge($traits, class_uses($c) ?: []);
            $c = get_parent_class($c);
        }

        return array_values(array_unique($traits));
    }

    /**
     * Coerce a JSON value to match a declared property type descriptor.
     *
     * @param  array<string, mixed>  $descriptor
     * @param  mixed  $value
     * @return mixed
     */
    private function castValue(array $descriptor, $value)
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

                // Unknown value — keep the raw string (union type allows it)
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
     * Coerce a JSON-decoded scalar to the declared PHP scalar type.
     *
     * Strict-types + json_decode mismatch: a JSON field that came back as int
     * (e.g. `"42"` decoded numerically somewhere upstream, or `42` declared as
     * `string $id` on the resource) must be normalized before assignment or a
     * `TypeError` is raised under strict_types.
     *
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

    private function holdsEmbeddedResources(object $resource, string $key, $value): bool
    {
        return $key === '_embedded'
            && ! is_null($value)
            && $resource instanceof EmbeddedResourcesContract;
    }

    private function parseEmbeddedResources(
        Connector $connector,
        object $resource,
        object $embedded,
        Response $response
    ): object {
        $result = new \stdClass;

        foreach ($embedded as $resourceKey => $resourceData) {
            $collectionOrResourceClass = $resource->getEmbeddedResourcesMap()[$resourceKey] ?? null;

            if (is_null($collectionOrResourceClass)) {
                throw new EmbeddedResourcesNotParseableException(
                    'Resource '.get_class($resource)." does not have a mapping for embedded resource {$resourceKey}"
                );
            }

            $result->{$resourceKey} = is_subclass_of($collectionOrResourceClass, BaseResource::class)
                ? $this->hydrate(
                    ResourceFactory::create($connector, $collectionOrResourceClass),
                    $resourceData,
                    $response
                )
                : $this->hydrateCollection(
                    ResourceFactory::createCollection($connector, $collectionOrResourceClass),
                    $resourceData,
                    $response
                );
        }

        return $result;
    }
}
