<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\ProfileStatus;

class Profile extends BaseResource
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
     * Test or live mode
     *
     * @var string
     */
    public $mode;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $website;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $phone;

    /**
     * See https://docs.mollie.com/reference/v2/profiles-api/get-profile
     *
     * @var int
     */
    public $categoryCode;

    /**
     * @var string
     */
    public $status;

    /**
     * @var object
     */
    public $review;

    /**
     * UTC datetime the profile was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string
     */
    public $createdAt;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * @return bool
     */
    public function isUnverified()
    {
        return $this->status == ProfileStatus::STATUS_UNVERIFIED;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return $this->status == ProfileStatus::STATUS_VERIFIED;
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->status == ProfileStatus::STATUS_BLOCKED;
    }

    /**
     * @return Profile
     * @throws ApiException
     */
    public function update()
    {
        if (!isset($this->_links->self->href)) {
            return $this;
        }

        $body = json_encode(array(
            "name" => $this->name,
            "website" => $this->website,
            "email" => $this->email,
            "phone" => $this->phone,
            "categoryCode" => $this->categoryCode,
            "mode" => $this->mode,
        ));

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_PATCH, $this->_links->self->href, $body);

        return ResourceFactory::createFromApiResult($result, new Profile($this->client));
    }

    /**
     * Retrieves all chargebacks associated with this profile
     *
     * @return ChargebackCollection
     * @throws ApiException
     */
    public function chargebacks()
    {
        if (!isset($this->_links->chargebacks->href)) {
            return new ChargebackCollection($this->client, 0, null);
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->chargebacks->href);

        return ResourceFactory::createCursorResourceCollection(
            $this->client,
            $result->_embedded->chargebacks,
            Chargeback::class,
            $result->_links
        );
    }

    /**
     * Retrieves all methods activated on this profile
     *
     * @return MethodCollection
     * @throws ApiException
     */
    public function methods()
    {
        if (!isset($this->_links->methods->href)) {
            return new MethodCollection(0, null);
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->methods->href);

        return ResourceFactory::createCursorResourceCollection(
            $this->client,
            $result->_embedded->methods,
            Method::class,
            $result->_links
        );
    }

    /**
     * Enable a payment method for this profile.
     *
     * @param string $methodId
     * @param array $data
     * @return Method
     * @throws ApiException
     */
    public function enableMethod($methodId, array $data = [])
    {
        return $this->client->profileMethods->createFor($this, $methodId, $data);
    }

    /**
     * Disable a payment method for this profile.
     *
     * @param string $methodId
     * @param array $data
     * @return Method
     * @throws ApiException
     */
    public function disableMethod($methodId, array $data = [])
    {
        return $this->client->profileMethods->deleteFor($this, $methodId, $data);
    }

    /**
     * Retrieves all payments associated with this profile
     *
     * @return PaymentCollection
     * @throws ApiException
     */
    public function payments()
    {
        if (!isset($this->_links->payments->href)) {
            return new PaymentCollection($this->client, 0, null);
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->payments->href);

        return ResourceFactory::createCursorResourceCollection(
            $this->client,
            $result->_embedded->methods,
            Method::class,
            $result->_links
        );
    }

    /**
     * Retrieves all refunds associated with this profile
     *
     * @return RefundCollection
     * @throws ApiException
     */
    public function refunds()
    {
        if (!isset($this->_links->refunds->href)) {
            return new RefundCollection($this->client, 0, null);
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->refunds->href);

        return ResourceFactory::createCursorResourceCollection(
            $this->client,
            $result->_embedded->refunds,
            Refund::class,
            $result->_links
        );
    }
}