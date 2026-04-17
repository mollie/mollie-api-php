<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Types\ProfileStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
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
        return $this->status === ProfileStatus::UNVERIFIED;
    }

    public function isVerified(): bool
    {
        return $this->status === ProfileStatus::VERIFIED;
    }

    public function isBlocked(): bool
    {
        return $this->status === ProfileStatus::BLOCKED;
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
        $href = $this->_links->chargebacks->href ?? "profiles/{$this->id}/chargebacks";

        return $this
            ->connector
            ->send((new DynamicGetRequest($href))->setHydratableResource(ChargebackCollection::class));
    }

    /**
     * Retrieves all methods activated on this profile
     *
     * @throws ApiException
     */
    public function methods(): MethodCollection
    {
        $href = $this->_links->methods->href ?? "profiles/{$this->id}/methods";

        return $this
            ->connector
            ->send((new DynamicGetRequest($href))->setHydratableResource(MethodCollection::class));
    }

    /**
     * Enable a payment method for this profile.
     *
     *
     * @throws ApiException
     */
    public function enableMethod(string $methodId): Method
    {
        return $this->connector->profileMethods->createFor($this, $methodId);
    }

    /**
     * Disable a payment method for this profile.
     *
     *
     * @throws ApiException
     */
    public function disableMethod(string $methodId): void
    {
        $this->connector->profileMethods->deleteFor($this, $methodId);
    }

    /**
     * Retrieves all payments associated with this profile
     *
     * @throws ApiException
     */
    public function payments(): PaymentCollection
    {
        $href = $this->_links->payments->href ?? "profiles/{$this->id}/payments";

        return $this
            ->connector
            ->send((new DynamicGetRequest($href))->setHydratableResource(PaymentCollection::class));
    }

    /**
     * Retrieves all refunds associated with this profile
     *
     * @throws ApiException
     */
    public function refunds(): RefundCollection
    {
        $href = $this->_links->refunds->href ?? "profiles/{$this->id}/refunds";

        return $this
            ->connector
            ->send((new DynamicGetRequest($href))->setHydratableResource(RefundCollection::class));
    }
}
