<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Partner extends BaseResource
{
    public string $partnerType;

    public ?bool $isCommissionPartner = null;

    /** @var array<mixed>|null */
    public ?array $userAgentTokens = null;

    public ?string $partnerContractSignedAt = null;

    public bool $partnerContractUpdateAvailable;

    public ?string $partnerContractExpiresAt = null;

    /**
     * @var \stdClass
     */
    public $_links;
}
