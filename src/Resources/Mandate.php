<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\MandateStatus;

class Mandate extends BaseResource
{
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
    public $mode;

    /**
     * @var string
     */
    public $method;

    /**
     * @var \stdClass|null
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
     * @var \stdClass
     */
    public $_links;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->status === MandateStatus::VALID;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === MandateStatus::PENDING;
    }

    /**
     * @return bool
     */
    public function isInvalid(): bool
    {
        return $this->status === MandateStatus::INVALID;
    }

    /**
     * Revoke the mandate
     *
     * @return null|Mandate
     */
    public function revoke(): ?Mandate
    {
        if (! isset($this->_links->self->href)) {
            return $this;
        }

        $body = null;
        if ($this->client->usesOAuth()) {
            $body = json_encode([
                "testmode" => $this->mode === "test" ? true : false,
            ]);
        }

        $result = $this->client->performHttpCallToFullUrl(
            MollieApiClient::HTTP_DELETE,
            $this->_links->self->href,
            $body
        );

        /** @var null|Mandate */
        return $result->isEmpty()
            ? null
            : ResourceFactory::createFromApiResult($this->client, $result->decode(), self::class);
    }
}
