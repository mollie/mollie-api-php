<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetTerminalPairingCodeRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Resources\TerminalPairingCode;
use Mollie\Api\Types\TerminalPairingCodeStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Contract: mollie/openapi@cfbba47b874b90ea54dc5e2643d163b8bc527902.
 */
class TerminalPairingCodeTest extends TestCase
{
    #[Test]
    public function known_status_hydrates_to_the_enum_case(): void
    {
        $code = $this->hydrate(['status' => 'active']);

        $this->assertSame(TerminalPairingCodeStatus::Active, $code->status);
        $this->assertTrue($code->isActive());
        $this->assertFalse($code->isExpired());
    }

    #[Test]
    public function unknown_status_stays_a_string_and_helpers_return_false(): void
    {
        $code = $this->hydrate(['status' => 'status-from-the-future']);

        $this->assertSame('status-from-the-future', $code->status);
        $this->assertFalse($code->isActive());
        $this->assertFalse($code->isExpired());
        $this->assertFalse($code->isRevoked());
    }

    #[DataProvider('dpHelpers')]
    #[Test]
    public function helpers_accept_both_the_case_and_the_raw_value(TerminalPairingCodeStatus $status, string $helper): void
    {
        $fromCase = new TerminalPairingCode($this->createMock(MollieApiClient::class));
        $fromCase->status = $status;

        $fromString = new TerminalPairingCode($this->createMock(MollieApiClient::class));
        $fromString->status = $status->value;

        $this->assertTrue($fromCase->{$helper}());
        $this->assertTrue($fromString->{$helper}());
    }

    public static function dpHelpers(): array
    {
        return [
            [TerminalPairingCodeStatus::Active, 'isActive'],
            [TerminalPairingCodeStatus::Expired, 'isExpired'],
            [TerminalPairingCodeStatus::Revoked, 'isRevoked'],
        ];
    }

    #[Test]
    public function the_bundled_fixture_hydrates_the_status(): void
    {
        $client = new MockMollieClient([
            GetTerminalPairingCodeRequest::class => MockResponse::ok('terminal-pairing-code'),
        ]);

        $code = $client->send(new GetTerminalPairingCodeRequest('termpc_x'));

        $this->assertInstanceOf(TerminalPairingCodeStatus::class, $code->status);
    }

    private function hydrate(array $data): TerminalPairingCode
    {
        $code = new TerminalPairingCode($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($code, ['resource' => 'terminal-pairing-code', 'id' => 'termpc_x'] + $data, $this->createMock(Response::class));

        return $code;
    }
}
