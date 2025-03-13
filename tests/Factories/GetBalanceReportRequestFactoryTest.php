<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetBalanceReportRequestFactory;
use Mollie\Api\Http\Requests\GetBalanceReportRequest;
use PHPUnit\Framework\TestCase;

class GetBalanceReportRequestFactoryTest extends TestCase
{
    private const BALANCE_ID = 'bal_12345';

    /** @test */
    public function create_returns_balance_report_request_object_with_full_data()
    {
        $request = GetBalanceReportRequestFactory::new(self::BALANCE_ID)
            ->withQuery([
                'from' => '2024-01-01',
                'until' => '2024-03-31',
                'grouping' => 'transaction-categories',
            ])
            ->create();

        $this->assertInstanceOf(GetBalanceReportRequest::class, $request);
    }

    /** @test */
    public function create_returns_balance_report_request_object_with_minimal_data()
    {
        $request = GetBalanceReportRequestFactory::new(self::BALANCE_ID)
            ->withQuery([
                'from' => '2024-01-01',
                'until' => '2024-03-31',
            ])
            ->create();

        $this->assertInstanceOf(GetBalanceReportRequest::class, $request);
    }

    /** @test */
    public function create_throws_exception_when_required_fields_are_missing()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "from" and "until" fields are required.');

        GetBalanceReportRequestFactory::new(self::BALANCE_ID)
            ->create();
    }

    /** @test */
    public function create_throws_exception_when_from_field_is_missing()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "from" and "until" fields are required.');

        GetBalanceReportRequestFactory::new(self::BALANCE_ID)
            ->withQuery([
                'until' => '2024-03-31',
            ])
            ->create();
    }

    /** @test */
    public function create_throws_exception_when_until_field_is_missing()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "from" and "until" fields are required.');

        GetBalanceReportRequestFactory::new(self::BALANCE_ID)
            ->withQuery([
                'from' => '2024-01-01',
            ])
            ->create();
    }
}
