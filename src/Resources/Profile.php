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
     * This parameter is deprecated and will be removed in 2022. Please use the businessCategory parameter instead.
     *
     * @deprecated
     * @var int|null
     */
    public $categoryCode;

    /**
     * See https://docs.mollie.com/reference/v2/profiles-api/get-profile
     *
     * @var string|null
     */
    public $businessCategory;

    /**
     * @var string
     */
    public $status;

    /**
     * @var \stdClass
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
     * @var \stdClass
     */
    public $_links;

    /**
     * @return bool
     */
    public function isUnverified(): bool
    {
        return $this->status == ProfileStatus::UNVERIFIED;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->status == ProfileStatus::VERIFIED;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->status == ProfileStatus::BLOCKED;
    }

    /**
     * @return null|Profile
     * @throws ApiException
     */
    public function update(): ?Profile
    {
        $body = [
            "name" => $this->name,
            "website" => $this->website,
            "email" => $this->email,
            "phone" => $this->phone,
            "businessCategory" => $this->businessCategory,
            "mode" => $this->mode,
        ];

        return $this->client->profiles->update($this->id, $body);
    }

    /**
     * Retrieves all chargebacks associated with this profile
     *
     * @return ChargebackCollection
     * @throws ApiException
     */
    public function chargebacks(): ChargebackCollection
    {
        if (! isset($this->_links->chargebacks->href)) {
            return new ChargebackCollection($this->client);
        }

        $result = $this
            ->client
            ->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->chargebacks->href)
            ->decode();

        /** @var ChargebackCollection */
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
    public function methods(): MethodCollection
    {
        if (! isset($this->_links->methods->href)) {
            return new MethodCollection($this->client);
        }

        $result = $this
            ->client
            ->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->methods->href)
            ->decode();

        /** @var MethodCollection */
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
    public function enableMethod($methodId, array $data = []): Method
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
    public function disableMethod($methodId, array $data = []): ?Method
    {
        return $this->client->profileMethods->deleteFor($this, $methodId, $data);
    }

    /**
     * Retrieves all payments associated with this profile
     *
     * @return PaymentCollection
     * @throws ApiException
     */
    public function payments(): PaymentCollection
    {
        if (! isset($this->_links->payments->href)) {
            return new PaymentCollection($this->client);
        }

        $result = $this
            ->client
            ->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->payments->href)
            ->decode();

        /** @var PaymentCollection */
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
    public function refunds(): RefundCollection
    {
        if (! isset($this->_links->refunds->href)) {
            return new RefundCollection($this->client);
        }

        $result = $this
            ->client
            ->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->refunds->href)
            ->decode();

        /** @var RefundCollection */
        return ResourceFactory::createCursorResourceCollection(
            $this->client,
            $result->_embedded->refunds,
            Refund::class,
            $result->_links
        );
    }
}
