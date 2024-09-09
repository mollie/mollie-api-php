<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\DynamicGetRequest;
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
     *
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
     *
     * @var string
     */
    public $createdAt;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isUnverified(): bool
    {
        return $this->status == ProfileStatus::UNVERIFIED;
    }

    public function isVerified(): bool
    {
        return $this->status == ProfileStatus::VERIFIED;
    }

    public function isBlocked(): bool
    {
        return $this->status == ProfileStatus::BLOCKED;
    }

    /**
     * @throws ApiException
     */
    public function update(): ?Profile
    {
        $body = [
            'name' => $this->name,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'businessCategory' => $this->businessCategory,
            'mode' => $this->mode,
        ];

        return $this->connector->profiles->update($this->id, $body);
    }

    /**
     * Retrieves all chargebacks associated with this profile
     *
     * @throws ApiException
     */
    public function chargebacks(): ChargebackCollection
    {
        if (! isset($this->_links->chargebacks->href)) {
            return new ChargebackCollection($this->connector);
        }

        return $this
            ->connector
            ->send(new DynamicGetRequest(
                $this->_links->chargebacks->href,
                ChargebackCollection::class
            ));
    }

    /**
     * Retrieves all methods activated on this profile
     *
     * @throws ApiException
     */
    public function methods(): MethodCollection
    {
        if (! isset($this->_links->methods->href)) {
            return new MethodCollection($this->connector);
        }

        return $this
            ->connector
            ->send(new DynamicGetRequest(
                $this->_links->methods->href,
                MethodCollection::class
            ));
    }

    /**
     * Enable a payment method for this profile.
     *
     * @param  string  $methodId
     *
     * @throws ApiException
     */
    public function enableMethod($methodId, array $data = []): Method
    {
        return $this->connector->profileMethods->createFor($this, $methodId, $data);
    }

    /**
     * Disable a payment method for this profile.
     *
     * @param  string  $methodId
     *
     * @throws ApiException
     */
    public function disableMethod($methodId, array $data = []): ?Method
    {
        return $this->connector->profileMethods->deleteFor($this, $methodId, $data);
    }

    /**
     * Retrieves all payments associated with this profile
     *
     * @throws ApiException
     */
    public function payments(): PaymentCollection
    {
        if (! isset($this->_links->payments->href)) {
            return new PaymentCollection($this->connector);
        }

        return $this
            ->connector
            ->send(new DynamicGetRequest(
                $this->_links->payments->href,
                PaymentCollection::class
            ));
    }

    /**
     * Retrieves all refunds associated with this profile
     *
     * @throws ApiException
     */
    public function refunds(): RefundCollection
    {
        if (! isset($this->_links->refunds->href)) {
            return new RefundCollection($this->connector);
        }

        return $this
            ->connector
            ->send(new DynamicGetRequest(
                $this->_links->refunds->href,
                RefundCollection::class
            ));
    }
}
