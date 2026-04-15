<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\CapabilityStatus;

class Capability extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     *
     * @example payments
     */
    public $name;

    /**
     * @var array
     */
    public $requirements;

    /**
     * @var string
     *
     * possible values: disabled, pending, enabled
     *
     * @example enabled
     */
    public $status;

    /**
     * @var string
     */
    public $statusReason;

    /**
     * @var string
     */
    public $organizationId;

    /**
     * Links to help navigate through the Mollie API and related resources.
     *
     * @var \stdClass
     */
    public $_links;

    public function isEnabled()
    {
        return $this->status === CapabilityStatus::Enabled->value;
    }

    public function isPending()
    {
        return $this->status === CapabilityStatus::Pending->value;
    }

    public function isDisabled()
    {
        return $this->status === CapabilityStatus::Disabled->value;
    }
}
