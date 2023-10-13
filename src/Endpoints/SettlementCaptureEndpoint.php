<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;

class SettlementCaptureEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "settlements_captures";

    /**
     * @inheritDoc
     */
    protected function getResourceObject()
    {
        return new Capture($this->client);
    }

    protected function getResourceCollectionObject($count, $_links)
    {
        return new CaptureCollection($this->client, $count, $_links);
    }

    /**
     * Retrieves a collection of Settlement Captures from Mollie.
     *
     * @param string $settlementId
     * @param string|null $from The first capture ID you want to include in your list.
     * @param int|null $limit
     * @param array $parameters
     *
     * @return mixed
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId(string $settlementId, string $from = null, int $limit = null, array $parameters = [])
    {
        $this->parentId = $settlementId;

        return $this->rest_list($from, $limit, $parameters);
    }
}
