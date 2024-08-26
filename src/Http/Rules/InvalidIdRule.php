<?php

namespace Mollie\Api\Http\Rules;

use Mollie\Api\Contracts\Rule;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\Request;

class InvalidIdRule implements Rule
{
    protected string $id;

    protected string $prefix;

    public function __construct(string $id, string $prefix)
    {
        $this->id = $id;
        $this->prefix = $prefix;
    }

    public function validate(Request $request): void {
        if (strpos($this->id, $this->prefix) !== 0) {
            $resourceType = $this->getResourceType($request);

            throw new ApiException("Invalid {$resourceType} ID: '{$this->id}'. A resource ID should start with '" . $this->prefix . "'.");
        }
    }

    public function getResourceType(Request $request): string
    {
        $classBasename = basename(str_replace("\\", "/", $request::getTargetResourceClass()));

        return strtolower($classBasename);
    }
}
