<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Data\DataCollection;
use ReflectionClass;
use ReflectionParameter;

trait ComposableFromArray
{
    public static function fromArray($data = []): self
    {
        $data = $data instanceof Arrayable ? $data->toArray() : $data;

        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor ? $constructor->getParameters() : [];

        return DataCollection::wrap($parameters)
            ->map(function (ReflectionParameter $parameter) use ($data) {
                $name = $parameter->getName();

                if (array_key_exists($name, $data)) {
                    return $data[$name];
                }

                if ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }

                return null;
            })
            ->pipe(fn (DataCollection $data) => $reflection->newInstanceArgs($data->toArray()));
    }
}
