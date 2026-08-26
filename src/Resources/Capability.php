<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\CapabilityStatus;
use Mollie\Api\Utils\Utility;

class Capability extends BaseResource
{

    public string $name;

    /** @var array<string> */
    public array $requirements;

    public CapabilityStatus|string $status;

    /**
     * Required by the API but null once no reason applies.
     */
    public ?string $statusReason;

    /**
     * Not part of the Capability API response; hydrated only when a payload carries it.
     */
    public ?string $organizationId = null;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isUnrequested(): bool
    {
        return Utility::equals($this->status, CapabilityStatus::Unrequested);
    }

    public function isEnabled(): bool
    {
        return Utility::equals($this->status, CapabilityStatus::Enabled);
    }

    public function isPending(): bool
    {
        return Utility::equals($this->status, CapabilityStatus::Pending);
    }

    public function isDisabled(): bool
    {
        return Utility::equals($this->status, CapabilityStatus::Disabled);
    }
}
