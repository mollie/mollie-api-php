<?php

declare(strict_types=1);

namespace Tests\Factories;

use Mollie\Api\Factories\RequestTerminalPairingCodeRequestFactory;
use Mollie\Api\Types\TerminalPairingCodeQuery;
use PHPUnit\Framework\TestCase;

class RequestTerminalPairingCodeRequestFactoryTest extends TestCase
{
    /** @test */
    public function it_can_create_request_with_payload()
    {
        $request = RequestTerminalPairingCodeRequestFactory::new()
            ->withPayload(['profileId' => 'pfl_jA9bC4DkFj3G'])
            ->create();

        $this->assertEquals(['profileId' => 'pfl_jA9bC4DkFj3G'], $request->payload()->all());
    }

    /** @test */
    public function it_can_create_request_with_qr_code_include_from_include_query()
    {
        $request = RequestTerminalPairingCodeRequestFactory::new()
            ->withPayload(['profileId' => 'pfl_jA9bC4DkFj3G'])
            ->withQuery(['include' => TerminalPairingCodeQuery::INCLUDE_QR_CODE])
            ->create();

        $this->assertEquals(TerminalPairingCodeQuery::INCLUDE_QR_CODE, $request->query()->get('include'));
    }

    /** @test */
    public function it_can_create_request_with_qr_code_include_from_boolean_query()
    {
        $request = RequestTerminalPairingCodeRequestFactory::new()
            ->withPayload(['profileId' => 'pfl_jA9bC4DkFj3G'])
            ->withQuery(['includeQrCode' => true])
            ->create();

        $this->assertEquals(TerminalPairingCodeQuery::INCLUDE_QR_CODE, $request->query()->get('include'));
    }
}
