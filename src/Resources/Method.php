<?php

namespace Mollie\Api\Resources;

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
     * @var object
     */
    public $minimumAmount;

    /**
     * An object containing value and currency. It represents the maximum payment amount allowed when using this
     * payment method.
     *
     * @var object
     */
    public $maximumAmount;

    /**
     * The $image->size1x and $image->size2x to display the payment method logo.
     *
     * @var object
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
     * @var object[]
     */
    public $_links;

    /**
     * Get the issuer value objects
     *
     * @return IssuerCollection
     */
    public function issuers()
    {
        return ResourceFactory::createBaseResourceCollection(
            $this->client,
            $this->issuers,
            Issuer::class
        );
    }

    /**
     * Get the method price value objects.
     *
     * @return MethodPriceCollection
     */
    public function pricing()
    {
        return ResourceFactory::createBaseResourceCollection(
            $this->client,
            $this->pricing,
            MethodPrice::class
        );
    }
}
