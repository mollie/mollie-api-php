<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\GetBalanceRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\ResourceHydrator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BalanceTest extends TestCase
{
    /**
     * entity-balance.required at
     * mollie/openapi@cfbba47b874b90ea54dc5e2643d163b8bc527902 (2026-08-26):
     * resource, id, mode, createdAt, currency, description, availableAmount,
     * pendingAmount, status, _links. transferFrequency and transferThreshold
     * are optional; incomingAmount and outgoingAmount do not exist in the contract.
     */
    private const SPEC_MINIMUM = [
        'resource' => 'balance',
        'id' => 'bal_gVMhHKqSSRYJyPsuoPNFH',
        'mode' => 'live',
        'createdAt' => '2019-01-10T10:23:41+00:00',
        'currency' => 'EUR',
        'description' => 'Primary balance',
        'status' => 'active',
        'availableAmount' => ['value' => '0.00', 'currency' => 'EUR'],
        'pendingAmount' => ['value' => '12.50', 'currency' => 'EUR'],
        '_links' => [],
    ];

    #[Test]
    public function it_hydrates_the_spec_minimum_payload_and_reads_every_typed_property(): void
    {
        $balance = $this->hydrate(self::SPEC_MINIMUM);

        $this->assertSame('bal_gVMhHKqSSRYJyPsuoPNFH', $balance->id);
        $this->assertSame('live', $balance->mode);
        $this->assertSame('2019-01-10T10:23:41+00:00', $balance->createdAt);
        $this->assertSame('EUR', $balance->currency);
        $this->assertSame('active', $balance->status);
        $this->assertInstanceOf(Money::class, $balance->availableAmount);
        $this->assertInstanceOf(Money::class, $balance->pendingAmount);
        $this->assertSame('12.50', $balance->pendingAmount->value);
        $this->assertNull($balance->incomingAmount);
        $this->assertNull($balance->outgoingAmount);
        $this->assertNull($balance->transferFrequency);
        $this->assertNull($balance->transferThreshold);
        $this->assertNull($balance->transferReference);
    }

    #[Test]
    public function optional_transfer_settings_hydrate_when_present(): void
    {
        $balance = $this->hydrate(self::SPEC_MINIMUM + [
            'transferFrequency' => 'twice-a-month',
            'transferThreshold' => ['value' => '5.00', 'currency' => 'EUR'],
        ]);

        $this->assertSame('twice-a-month', $balance->transferFrequency);
        $this->assertInstanceOf(Money::class, $balance->transferThreshold);
        $this->assertSame('5.00', $balance->transferThreshold->value);
    }

    #[Test]
    public function the_bundled_fixture_still_hydrates(): void
    {
        $client = new MockMollieClient([
            GetBalanceRequest::class => MockResponse::ok('balance'),
        ]);

        $balance = $client->send(new GetBalanceRequest('bal_gVMhHKqSSRYJyPsuoPNFH'));

        $this->assertInstanceOf(Money::class, $balance->pendingAmount);
        $this->assertInstanceOf(Money::class, $balance->availableAmount);
    }

    /** @param array<string, mixed> $data */
    private function hydrate(array $data): Balance
    {
        $balance = new Balance($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($balance, $data, $this->createMock(Response::class));

        return $balance;
    }
}
