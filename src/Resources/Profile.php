<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Types\ProfileStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Profile extends BaseResource
{
    public string $id;

    /**
     * Test or live mode.
     */
    public string $mode;

    public string $name;

    public ?string $website = null;

    public ?string $email = null;

    public ?string $phone = null;

    /**
     * Deprecated — use businessCategory instead.
     *
     * @deprecated
     */
    public int|string|null $categoryCode = null;

    public ?string $businessCategory = null;

    public ProfileStatus|string|null $status = null;

    /**
     * @var \stdClass|null
     */
    public $review;

    /**
     * UTC datetime the profile was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     */
    public ?string $createdAt = null;

    /**
     * @var \stdClass|null
     */
    public $_links;

    public function isUnverified(): bool
    {
        return $this->status === ProfileStatus::Unverified;
    }

    public function isVerified(): bool
    {
        return $this->status === ProfileStatus::Verified;
    }

    public function isBlocked(): bool
    {
        return $this->status === ProfileStatus::Blocked;
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
            return ChargebackCollection::withResponse($this->response, $this->connector);
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->chargebacks->href))->setHydratableResource(ChargebackCollection::class));
    }

    /**
     * Retrieves all methods activated on this profile
     *
     * @throws ApiException
     */
    public function methods(): MethodCollection
    {
        if (! isset($this->_links->methods->href)) {
            return MethodCollection::withResponse($this->response, $this->connector);
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->methods->href))->setHydratableResource(MethodCollection::class));
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
        if (! isset($this->_links->payments->href)) {
            return PaymentCollection::withResponse($this->response, $this->connector);
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->payments->href))->setHydratableResource(PaymentCollection::class));
    }

    /**
     * Retrieves all refunds associated with this profile
     *
     * @throws ApiException
     */
    public function refunds(): RefundCollection
    {
        if (! isset($this->_links->refunds->href)) {
            return RefundCollection::withResponse($this->response, $this->connector);
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->refunds->href))->setHydratableResource(RefundCollection::class));
    }
}
