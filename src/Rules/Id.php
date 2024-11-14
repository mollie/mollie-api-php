<?php

namespace Mollie\Api\Rules;

use Closure;
use Mollie\Api\Contracts\Rule;
use Mollie\Api\Http\Request;

class Id implements Rule
{
    protected string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public static function startsWithPrefix(string $prefix): self
    {
        return new self($prefix);
    }

    public function validate($id, $context, Closure $fail): void
    {
        if (! $context instanceof Request) {
            $fail('The Id rule can only be used on a Request instance.');
        }

        if (strpos($id, $this->prefix) !== 0) {
            // @todo: message is wrong for child endpoints e.g. PaymentRefundsEndpointCollection
            $resourceType = $this->getResourceType($context);

            $fail("Invalid {$resourceType} ID: '{$id}'. A resource ID should start with '".$this->prefix."'.");
        }
    }

    public function getResourceType(Request $request): string
    {
        $classBasename = basename(str_replace('\\', '/', $request->getTargetResourceClass()));

        return strtolower($classBasename);
    }
}
