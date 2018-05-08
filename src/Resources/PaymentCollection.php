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
     * Return the next set of resources when available
     *
     * @return CursorCollection|null
     */
    public function next()
    {
        if(!isset($this->_links->next->href)) {
            return null;
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->next->href);

        $collection = new self($this->client, $this->count, $this->_links);

        foreach ($result->_embedded->{$collection->getCollectionResourceName()} as $dataResult) {
            $collection[] = ResourceFactory::createFromApiResult($dataResult, new Payment($this->client));
        }

        return $collection;
    }

    /**
     * Return the previous set of resources when available
     *
     * @return CursorCollection|null
     */
    public function previous()
    {
        if(!isset($this->_links->previous->href)) {
            return null;
        }

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_GET, $this->_links->previous->href);

        $collection = new self($this->client, $this->count, $this->_links);

        foreach ($result->_embedded->{$collection->getCollectionResourceName()} as $dataResult) {
            $collection[] = ResourceFactory::createFromApiResult($dataResult, new Payment($this->client));
        }

        return $collection;
    }
}
