<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\TerminalStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Terminal extends BaseResource
{

    public string $id;

    public string $profileId;

    public TerminalStatus|string $status;

    public string $brand;

    public string $model;

    public string $serialNumber;

    public string $currency;

    public string $description;

    public string $timezone;

    public string $locale;

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
        return $this->status === TerminalStatus::Pending;
    }

    public function isActive(): bool
    {
        return $this->status === TerminalStatus::Active;
    }

    public function isInactive(): bool
    {
        return $this->status === TerminalStatus::Inactive;
    }
}
