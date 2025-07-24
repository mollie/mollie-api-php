<?php

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Method extends BaseResource
{
    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;

    /**
     * More legible description of the payment method.
     *
     * @var string
     */
    public $description;

    /**
     * An object containing value and currency. It represents the minimum payment amount required to use this
     * payment method.
     *
     * @var \stdClass
     */
    public $minimumAmount;

    /**
     * An object containing value and currency. It represents the maximum payment amount allowed when using this
     * payment method.
     *
     * @var \stdClass
     */
    public $maximumAmount;

    /**
     * The $image->size1x and $image->size2x to display the payment method logo.
     *
     * @var \stdClass
     */
    public $image;

    /**
     * The issuers available for this payment method. Only for the methods iDEAL, KBC/CBC and gift cards.
     * Will only be filled when explicitly requested using the query string `include` parameter.
     *
     * @var array|object[]
     */
    public $issuers;

    /**
     * The pricing for this payment method. Will only be filled when explicitly requested using the query string
     * `include` parameter.
     *
     * @var array|object[]
     */
    public $pricing;

    /**
     * The activation status the method is in.
     * If the method has status "null", this value will be returned as a null value, not as a string.
     *
     * @var string | null
     */
    public $status;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * Get the issuer value objects
     */
    public function issuers(): IssuerCollection
    {
        /** @var IssuerCollection */
        $collection = ResourceFactory::createCollection(
            $this->connector,
            IssuerCollection::class
        );

        if ($this->issuers === null) {
            return $collection;
        }

        /** @var IssuerCollection */
        $collection = (new ResourceHydrator)->hydrateCollection(
            $collection,
            (array) $this->issuers,
            $this->response
        );

        return $collection;
    }

    /**
     * Get the method price value objects.
     */
    public function pricing(): MethodPriceCollection
    {
        /** @var MethodPriceCollection */
        $collection = ResourceFactory::createCollection(
            $this->connector,
            MethodPriceCollection::class
        );

        if ($this->pricing === null) {
            return $collection;
        }

        /** @var MethodPriceCollection */
        $collection = (new ResourceHydrator)->hydrateCollection(
            $collection,
            (array) $this->pricing,
            $this->response
        );

        return $collection;
    }
}
