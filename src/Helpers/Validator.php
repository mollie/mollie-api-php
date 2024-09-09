<?php

namespace Mollie\Api\Helpers;

use Mollie\Api\Contracts\ValidatableDataProvider;
use Mollie\Api\Exceptions\RequestValidationException;
use Mollie\Api\Helpers;
use Mollie\Api\Traits\Makeable;
use ReflectionProperty;

class Validator
{
    use Makeable;

    public function validate(ValidatableDataProvider $provider, array $additional = []): void
    {
        $rules = $provider->rules();

        [$validatableProperties, $propsWithRules] = $this->extractValidatableAndRules($provider, $rules);

        // Validate validatable properties
        foreach ($validatableProperties as $property) {
            $this->validate($property);
        }

        // Merge additional rules with the rulesWithValues array
        foreach ($additional as $key => $value) {
            if (array_key_exists($key, $rules)) {
                $propsWithRules[$key] = $value;
            }
        }

        // Validate properties with rules
        foreach ($propsWithRules as $key => $value) {
            $rules[$key]->validate($value, $provider, static function (string $message) {
                throw new RequestValidationException($message);
            });
        }
    }

    private function extractValidatableAndRules($provider, array $rules): array
    {
        $validatableProperties = [];
        $propsWithRules = [];

        /** @var ReflectionProperty $property */
        foreach (Helpers::getProperties($provider) as $property) {
            if ($property->isStatic() || ! $property->isInitialized($provider)) {
                continue;
            }

            $value = $this->extractValue($provider, $property);

            if ($value === null) {
                continue;
            }

            if ($value instanceof ValidatableDataProvider) {
                $validatableProperties[] = $value;
            } elseif (array_key_exists($property->getName(), $rules)) {
                $propsWithRules[$property->getName()] = $value;
            }
        }

        return [$validatableProperties, $propsWithRules];
    }

    private function extractValue($provider, ReflectionProperty $property)
    {
        $property->setAccessible(true);

        return $property->getValue($provider);
    }
}
