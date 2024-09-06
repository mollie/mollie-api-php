<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Validatable;
use Mollie\Api\Helpers;
use ReflectionProperty;

trait ValidatesProperties
{
    public function validate(): void
    {
        $rules = $this->rules();

        [$validatableProperties, $rulesWithValues] = $this->getValidatableAndRules($rules);

        // Perform validation using the returned arrays
        foreach ($validatableProperties as $property) {
            $property->validate();
        }

        foreach ($rulesWithValues as [$rule, $value]) {
            $rule->validate($this, $value);
        }
    }

    private function getValidatableAndRules(array $rules = []): array
    {
        $validatableProperties = [];
        $rulesWithValues = [];

        /** @var ReflectionProperty $property */
        foreach (Helpers::getProperties($this) as $property) {
            if ($property->isStatic() || ! $property->isInitialized($this)) {
                continue;
            }

            $value = $this->extractValue($property);

            if ($value === null) {
                continue;
            }

            if ($value instanceof Validatable) {
                $validatableProperties[] = $value;
            } elseif (array_key_exists($property->getName(), $rules)) {
                $rulesWithValues[] = [$rules[$property->getName()], $value];
            }
        }

        return [$validatableProperties, $rulesWithValues];
    }

    private function validateProperties(array $rules = []): void
    {
        /** @var ReflectionProperty $property */
        foreach (Helpers::getProperties($this) as $property) {
            if ($property->isStatic() || ! $property->isInitialized($this)) {
                continue;
            }

            $value = $this->extractValue($property);

            if ($value === null) {
                continue;
            }

            if ($value instanceof Validatable) {
                $value->validate();
            } elseif (array_key_exists($property->getName(), $rules)) {
                $rules[$property->getName()]->validate($this, $value);
            }
        }
    }

    private function extractValue(ReflectionProperty $property): mixed
    {
        return $property->getValue($this);
    }

    private function validateQuery(array $rules = []): void
    {
        if (empty($rules)) {
            return;
        }

        $nonNullValues = array_filter($this->query()->all());

        $queryToValidate = array_filter(
            $nonNullValues,
            fn ($_, $key) => array_key_exists($key, $rules),
            ARRAY_FILTER_USE_BOTH
        );

        foreach ($queryToValidate as $property => $value) {
            $rules[$property]->validate($this, $value);
        }
    }

    public function rules(): array
    {
        return [];
    }
}
