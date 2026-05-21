<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Types\PaymentMethodStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Method extends BaseResource
{
    public string $id;

    /**
     * More legible description of the payment method.
     */
    public string $description;

    /**
     * Minimum payment amount allowed when using this payment method.
     */
    public ?Money $minimumAmount = null;

    /**
     * Maximum payment amount allowed when using this payment method.
     */
    public ?Money $maximumAmount = null;

    /**
     * The $image->size1x and $image->size2x to display the payment method logo.
     *
     * @var \stdClass|null
     */
    public $image;

    /**
     * The issuers available for this payment method. Only for the methods iDEAL, KBC/CBC and gift cards.
     * Will only be filled when explicitly requested using the query string `include` parameter.
     *
     * @var array|null
     */
    public ?array $issuers = null;

    /**
     * The pricing for this payment method. Will only be filled when explicitly requested using the query string
     * `include` parameter.
     *
     * @var array|null
     */
    public ?array $pricing = null;

    /**
     * The activation status the method is in.
     */
    public PaymentMethodStatus|string|null $status = null;

    /**
     * @var \stdClass|null
     */
    public $_links;

    public function issuers(): IssuerCollection
    {
        /** @var IssuerCollection $collection */
        $collection = ResourceFactory::createCollection(
            $this->connector,
            IssuerCollection::class
        );

        if ($this->issuers === null) {
            return $collection;
        }

        /** @var IssuerCollection $collection */
        $collection = (new ResourceHydrator)->hydrateCollection(
            $collection,
            (array) $this->issuers,
            $this->getOrigin()
        );

        return $collection;
    }

    public function pricing(): MethodPriceCollection
    {
        /** @var MethodPriceCollection $collection */
        $collection = ResourceFactory::createCollection(
            $this->connector,
            MethodPriceCollection::class
        );

        if ($this->pricing === null) {
            return $collection;
        }

        /** @var MethodPriceCollection $collection */
        $collection = (new ResourceHydrator)->hydrateCollection(
            $collection,
            (array) $this->pricing,
            $this->getOrigin()
        );

        return $collection;
    }
}
