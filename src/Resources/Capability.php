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

    public string $statusReason;

    public string $organizationId;

    /**
     * @var \stdClass
     */
    public $_links;

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
