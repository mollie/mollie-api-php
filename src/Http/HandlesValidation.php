<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\Rule;
use Mollie\Api\Http\Requests\Request;

/**
 * @mixin Endpoint
 */
trait HandlesValidation
{
    protected array $rules = [];

    protected function validateWith(...$rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    protected function validate(Request $request): void
    {
        /** @var Rule $rule */
        foreach ($this->rules as $rule) {
            $rule->validate($request);
        }
    }

    protected function rules(): array
    {
        return [];
    }

    private function getRules(): array
    {
        return array_merge($this->rules(), $this->rules);
    }
}
