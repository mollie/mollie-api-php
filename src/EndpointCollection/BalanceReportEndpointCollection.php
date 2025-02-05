<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\GetBalanceReportRequestFactory;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceReport;
use Mollie\Api\Utils\Utility;

class BalanceReportEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a balance report for the provided balance id and parameters.
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function getForId(string $balanceId, array $query = []): ?BalanceReport
    {
        $testmode = Utility::extractBool($query, 'testmode', false);

        $request = GetBalanceReportRequestFactory::new($balanceId)
            ->withQuery($query)
            ->create();

        /** @var BalanceReport */
        return $this->send($request->test($testmode));
    }

    /**
     * Retrieve the primary balance.
     * This is the balance of your account’s primary currency, where all payments are settled to by default.
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function getForPrimary(array $query = []): ?BalanceReport
    {
        return $this->getForId('primary', $query);
    }

    /**
     * Retrieve a balance report for the provided balance resource and parameters.
     *
     * @throws \Mollie\Api\Exceptions\RequestException
     */
    public function getFor(Balance $balance, array $query = []): ?BalanceReport
    {
        return $this->getForId($balance->id, $query);
    }
}
