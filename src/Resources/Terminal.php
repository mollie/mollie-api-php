<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\TerminalStatus;
use Mollie\Api\Utils\Utility;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Terminal extends BaseResource
{

    public string $id;

    public string $profileId;

    public TerminalStatus|string $status;

    /**
     * Required by the API but may be null.
     */
    public ?string $brand;

    public ?string $model;

    public ?string $serialNumber;

    public string $currency;

    public string $description;

    /**
     * Not part of the Terminal API response; hydrated only when a payload carries it.
     */
    public ?string $timezone = null;

    public ?string $locale = null;

    public string $createdAt;

    public string $updatedAt;

    public ?string $disabledAt = null;

    public ?string $activatedAt = null;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isPending(): bool
    {
        return Utility::equals($this->status, TerminalStatus::Pending);
    }

    public function isActive(): bool
    {
        return Utility::equals($this->status, TerminalStatus::Active);
    }

    public function isInactive(): bool
    {
        return Utility::equals($this->status, TerminalStatus::Inactive);
    }
}
