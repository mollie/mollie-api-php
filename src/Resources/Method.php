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
        return $this->createResourceCollection($this->issuers, Issuer::class);
    }

    /**
     * Get the method price value objects.
     *
     * @return MethodPriceCollection
     */
    public function pricing()
    {
        return $this->createResourceCollection($this->pricing, MethodPrice::class);
    }

    /**
     * Create a resource collection from an array.
     *
     * @param array $input
     * @param string $resourceClass The full class namespace
     * @param null|object[] $_links
     * @param null $resourceCollectionClass If empty, appends 'Collection' to the `$resourceClass` to resolve the Collection class.
     * @return mixed
     */
    protected function createResourceCollection($input, $resourceClass, $_links = null, $resourceCollectionClass = null)
    {
        if (null === $resourceCollectionClass) {
            $resourceCollectionClass = $resourceClass.'Collection';
        }

        $data = new $resourceCollectionClass(count($input), $_links);
        foreach ($input as $item) {
            $data[] = ResourceFactory::createFromApiResult($item, new $resourceClass($this->client));
        }

        return $data;
    }
}
