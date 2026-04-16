<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\CapabilityStatus;

class Capability extends BaseResource
{

    public string $name;

    /** @var array<string> */
    public array $requirements;

    public CapabilityStatus|string $status;

    public string $statusReason;

    public string $organizationId;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isEnabled(): bool
    {
        return $this->status === CapabilityStatus::Enabled;
    }

    public function isPending(): bool
    {
        return $this->status === CapabilityStatus::Pending;
    }

    public function isDisabled(): bool
    {
        return $this->status === CapabilityStatus::Disabled;
    }
}
