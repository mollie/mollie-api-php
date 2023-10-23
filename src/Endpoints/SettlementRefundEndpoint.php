<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class SettlementRefundEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "settlements_refunds";

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new RefundCollection($this->client, $count, $_links);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceObject()
    {
        return new Refund($this->client);
    }

    /**
     * Retrieves a collection of Settlement Refunds from Mollie.
     *
     * @param string $settlementId
     * @param string|null $from The first refund ID you want to include in your list.
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
