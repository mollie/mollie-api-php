<?php

namespace Mollie\Api\Resources;

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

}