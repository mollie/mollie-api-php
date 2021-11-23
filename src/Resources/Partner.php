<?php

namespace Mollie\Api\Resources;

class Partner extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $partnerType;

    /**
     * @var bool|null
     */
    public $isCommissionPartner;

    /**
     * @var array|null
     */
    public $userAgentTokens;

    /**
     * @var string|null
     */
    public $partnerContractSignedAt;

    /**
     * @var bool|null
     */
    public $partnerContractUpdateAvailable;

    /**
     * @var \stdClass
     */
    public $_links;
}
