<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Requests\GetBalanceReportRequest;

class GetBalanceReportRequestFactory extends RequestFactory
{
    private string $balanceId;

    public function __construct(string $balanceId)
    {
        $this->balanceId = $balanceId;
    }

    public function create(): GetBalanceReportRequest
    {
        if (! $this->queryHas(['from', 'until'])) {
            throw new \LogicException('The "from" and "until" fields are required.');
        }

        /** @var DateTimeImmutable $from */
        $from = DateTimeImmutable::createFromFormat('Y-m-d', $this->query('from'));

        /** @var DateTimeImmutable $until */
        $until = DateTimeImmutable::createFromFormat('Y-m-d', $this->query('until'));

        return new GetBalanceReportRequest(
            $this->balanceId,
            $from,
            $until,
            $this->query('grouping'),
        );
    }
}
