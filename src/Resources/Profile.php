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
        $href = $this->_links->chargebacks->href ?? "profiles/{$this->id}/chargebacks";
        $query = isset($this->_links->chargebacks->href) ? [] : ['testmode' => $this->mode === 'test'];

        return $this
            ->connector
            ->send((new DynamicGetRequest($href, $query))->setHydratableResource(ChargebackCollection::class));
    }

    /**
     * Retrieves all methods activated on this profile
     *
     * @throws ApiException
     */
    public function methods(): MethodCollection
    {
        $href = $this->_links->methods->href ?? "profiles/{$this->id}/methods";
        $query = isset($this->_links->methods->href) ? [] : ['testmode' => $this->mode === 'test'];

        return $this
            ->connector
            ->send((new DynamicGetRequest($href, $query))->setHydratableResource(MethodCollection::class));
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
        $query = isset($this->_links->payments->href) ? [] : ['testmode' => $this->mode === 'test'];

        return $this
            ->connector
            ->send((new DynamicGetRequest($href, $query))->setHydratableResource(PaymentCollection::class));
    }

    /**
     * Retrieves all refunds associated with this profile
     *
     * @throws ApiException
     */
    public function refunds(): RefundCollection
    {
        $href = $this->_links->refunds->href ?? "profiles/{$this->id}/refunds";
        $query = isset($this->_links->refunds->href) ? [] : ['testmode' => $this->mode === 'test'];

        return $this
            ->connector
            ->send((new DynamicGetRequest($href, $query))->setHydratableResource(RefundCollection::class));
    }
}
