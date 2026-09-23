<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\IssuerCollection;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Types\PaymentMethodStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class MethodTest extends TestCase
{
    #[Test]
    public function issuers_null_works()
    {
        $method = new Method(new MockMollieClient);
        $method->setResponse($this->createMock(Response::class));
        $this->assertNull($method->issuers);

        $issuers = $method->issuers();

        $this->assertInstanceOf(IssuerCollection::class, $issuers);
        $this->assertCount(0, $issuers);
    }

    #[Test]
    public function known_status_hydrates_to_the_enum_case(): void
    {
        $this->assertSame(PaymentMethodStatus::Activated, $this->hydrate(['status' => 'activated'])->status);
        $this->assertSame(PaymentMethodStatus::PendingBoarding, $this->hydrate(['status' => 'pending-boarding'])->status);
    }

    #[Test]
    public function unknown_status_stays_a_string(): void
    {
        $this->assertSame('status-from-the-future', $this->hydrate(['status' => 'status-from-the-future'])->status);
    }

    #[Test]
    public function null_status_means_not_requested(): void
    {
        $this->assertNull($this->hydrate(['status' => null])->status);

        // mollie/openapi@cfbba47b874b90ea54dc5e2643d163b8bc527902: status is required
        // but nullable, so explicit null is the only way to get null.
        $this->assertFalse((new ReflectionClass(Method::class))->getProperty('status')->hasDefaultValue());
    }

    private function hydrate(array $data): Method
    {
        $method = new Method($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($method, ['resource' => 'method', 'id' => 'ideal', 'description' => 'iDEAL'] + $data, $this->createMock(Response::class));

        return $method;
    }
}
