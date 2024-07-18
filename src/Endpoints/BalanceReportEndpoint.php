<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceReport;
use Mollie\Api\Resources\ResourceFactory;

class BalanceReportEndpoint extends RestEndpoint
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "balances_report";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = BalanceReport::class;

    /**
     * Retrieve a balance report for the provided balance id and parameters.
     *
     * @param string $balanceId
     * @param array $parameters
     *
     * @return null|BalanceReport
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $balanceId, array $parameters = []): ?BalanceReport
    {
        $this->parentId = $balanceId;

        $response = $this->client->performHttpCall(
            self::REST_READ,
            $this->getResourcePath() . $this->buildQueryString($parameters)
        );

        if ($response->isEmpty()) {
            return null;
        }

        /** @var BalanceReport */
        return ResourceFactory::createFromApiResult($this->client, $response->decode(), static::getResourceClass());
    }

    /**
     * Retrieve the primary balance.
     * This is the balance of your account’s primary currency, where all payments are settled to by default.
     *
     * @param array $parameters
     *
     * @return BalanceReport
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForPrimary(array $parameters = []): BalanceReport
    {
        return $this->getForId("primary", $parameters);
    }


    /**
     * Retrieve a balance report for the provided balance resource and parameters.
     *
     * @param Balance $balance
     * @param array $parameters
     * @return BalanceReport
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Balance $balance, array $parameters = []): BalanceReport
    {
        return $this->getForId($balance->id, $parameters);
    }
}
