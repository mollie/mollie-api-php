<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\TerminalPairingCodeStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class TerminalPairingCode extends BaseResource
{
    use HasMode;

    public string $id;

    public string $code;

    public string $profileId;

    public TerminalPairingCodeStatus|string $status;

    /**
     * @var \stdClass|null
     */
    public $details = null;

    public string $expiresAt;

    public ?string $revokedAt = null;

    public string $createdAt;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isActive(): bool
    {
        return $this->status === TerminalPairingCodeStatus::Active;
    }

    public function isExpired(): bool
    {
        return $this->status === TerminalPairingCodeStatus::Expired;
    }

    public function isRevoked(): bool
    {
        return $this->status === TerminalPairingCodeStatus::Revoked;
    }
}
