<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\MandateStatus;

class Mandate extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $method;

    /**
     * @var object|null
     */
    public $details;

    /**
     * @var string
     */
    public $customerId;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var string
     */
    public $mandateReference;

    /**
     * Date of signature, for example: 2018-05-07
     *
     * @var string
     */
    public $signatureDate;

    /**
     * @var object
     */
    public $_links;

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->status === MandateStatus::STATUS_VALID;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->status === MandateStatus::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isInvalid()
    {
        return $this->status === MandateStatus::STATUS_INVALID;
    }

    /**
     * Revoke the mandate
     *
     * @return null
     */
    public function revoke()
    {
        if (!isset($this->_links->self->href)) {
            return $this;
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_DELETE, $this->_links->self->href);

        return $result;
    }

}