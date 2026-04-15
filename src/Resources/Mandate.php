<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\MandateStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Mandate extends BaseResource
{
    use HasMode;

    public string $id;

    public MandateStatus|string|null $status = null;

    public string $mode;

    public ?string $method = null;

    /**
     * @var \stdClass|null
     */
    public $details;

    public ?string $customerId = null;

    public ?string $createdAt = null;

    public ?string $mandateReference = null;

    /**
     * Date of signature, for example: 2018-05-07.
     */
    public ?string $signatureDate = null;

    /**
     * @var \stdClass|null
     */
    public $_links;

    public function isValid(): bool
    {
        return $this->status === MandateStatus::Valid;
    }

    public function isPending(): bool
    {
        return $this->status === MandateStatus::Pending;
    }

    public function isInvalid(): bool
    {
        return $this->status === MandateStatus::Invalid;
    }

    /**
     * Revoke the mandate.
     */
    public function revoke(): void
    {
        if (! isset($this->customerId)) {
            return;
        }

        $this
            ->connector
            ->mandates
            ->revokeForId(
                $this->customerId,
                $this->id,
                $this->isInTestmode()
            );
    }
}
