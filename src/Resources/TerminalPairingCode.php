<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Types\TerminalPairingCodeStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class TerminalPairingCode extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * The unique identifier of the pairing code.
     *
     * @example termpc_R7gX5Ea9bC4DkFj3G
     *
     * @var string
     */
    public $id;

    /**
     * The mode the pairing code was created in.
     *
     * @var string
     */
    public $mode;

    /**
     * The human-readable pairing code to be entered on the terminal.
     *
     * @example 20eb5ca1f78b48ae9e2b
     *
     * @var string
     */
    public $code;

    /**
     * The ID of the profile the terminal is being paired with.
     *
     * @var string
     */
    public $profileId;

    /**
     * The status of the pairing code: active, expired, or revoked.
     *
     * @var string
     */
    public $status;

    /**
     * Additional pairing code data, present only when requested via the `include` parameter.
     *
     * @var \stdClass|null
     */
    public $details;

    /**
     * UTC datetime the pairing code expires, in ISO 8601 format.
     *
     * @example "2026-03-10T10:00:00+00:00"
     *
     * @var string
     */
    public $expiresAt;

    /**
     * UTC datetime the pairing code was revoked, in ISO 8601 format. Null if not revoked.
     *
     * @example "2025-12-10T10:03:23+00:00"
     *
     * @var string|null
     */
    public $revokedAt;

    /**
     * UTC datetime the pairing code was created, in ISO 8601 format.
     *
     * @example "2025-12-10T10:00:00+00:00"
     *
     * @var string
     */
    public $createdAt;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isActive(): bool
    {
        return $this->status === TerminalPairingCodeStatus::ACTIVE;
    }

    public function isExpired(): bool
    {
        return $this->status === TerminalPairingCodeStatus::EXPIRED;
    }

    public function isRevoked(): bool
    {
        return $this->status === TerminalPairingCodeStatus::REVOKED;
    }
}
