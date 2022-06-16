<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\BalanceReport;
use Mollie\Api\Resources\ResourceFactory;

class BalanceReportEndpoint extends EndpointAbstract
{
    protected $resourcePath = "balances_report";

    /**
     * @inheritDoc
     */
    protected function getResourceObject()
    {
        return new BalanceReport($this->client);
    }

    public function getForBalanceId($balanceId, $parameters = [])
    {
        $this->parentId = $balanceId;

        $result = $this->client->performHttpCall(
            self::REST_READ,
            $this->getResourcePath() . $this->buildQueryString($parameters)
        );

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    public function getForPrimaryBalance($parameters = [])
    {
        return $this->getForBalanceId("primary", $parameters);
    }

    public function getForBalance($balance, $parameters = [])
    {
        return $this->getForBalanceId($balance->id, $parameters);
    }
}
