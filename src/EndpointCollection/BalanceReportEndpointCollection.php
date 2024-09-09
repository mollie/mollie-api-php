<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\GetBalanceReportQueryFactory;
use Mollie\Api\Http\Query\GetBalanceReportQuery;
use Mollie\Api\Http\Requests\GetBalanceReportRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceReport;

class BalanceReportEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a balance report for the provided balance id and parameters.
     *
     * @param  array|GetBalanceReportQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForId(string $balanceId, $query = []): ?BalanceReport
    {
        $query = GetBalanceReportQueryFactory::new($query)
            ->create();

        /** @var BalanceReport */
        return $this->send(new GetBalanceReportRequest($balanceId, $query));
    }

    /**
     * Retrieve the primary balance.
     * This is the balance of your account’s primary currency, where all payments are settled to by default.
     *
     * @param  array|GetBalanceReportQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getForPrimary($query = []): BalanceReport
    {
        return $this->getForId('primary', $query);
    }

    /**
     * Retrieve a balance report for the provided balance resource and parameters.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function getFor(Balance $balance, array $parameters = []): BalanceReport
    {
        return $this->getForId($balance->id, $parameters);
    }
}
