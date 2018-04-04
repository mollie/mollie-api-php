<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;

class PaymentCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName()
    {
        return "payments";
    }

    /**
     * @throws ApiException
     *
     * @return PaymentCollection
     */
    public function next()
    {
        if(!isset($this->_links->next->href)) {
            throw new ApiException("There are no next payments.");
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->next->href);

        $collection = new self($this->client, $this->count, $this->_links);

        foreach ($result->_embedded->{$collection->getCollectionResourceName()} as $dataResult) {
            $collection[] = ResourceFactory::createFromApiResult($dataResult, new Payment($this->client));
        }

        return $collection;
    }

    /**
     * @throws ApiException
     *
     * @return PaymentCollection
     */
    public function previous()
    {
        if(!isset($this->_links->previous->href)) {
            throw new ApiException("There are no next payments.");
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->previous->href);

        $collection = new self($this->client, $this->count, $this->_links);

        foreach ($result->_embedded->{$collection->getCollectionResourceName()} as $dataResult) {
            $collection[] = ResourceFactory::createFromApiResult($dataResult, new Payment($this->client));
        }

        return $collection;
    }
}
