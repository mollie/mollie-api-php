<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\ResourceWrapper;

use function PHPStan\Testing\assertType;

/**
 * Static-analysis fixture, never executed. PHPStan analyses tests/ (phpstan.neon)
 * and reports every assertType() mismatch as a normal error, so this file is the
 * regression test for the generic send() contract. Pest only runs *Test.php files.
 */
final class HydratableRequestGenericsFixture
{
    public function assertions(MollieApiClient $client): void
    {
        // Canonical request: unchanged behaviour.
        $payment = $client->send(new GetPaymentRequest('tr_x'));
        assertType(Payment::class, $payment);

        // Wrapper decoration flows into send().
        $wrapped = $client->send((new GetPaymentRequest('tr_x'))->wrapInto(GenericsFixturePaymentWrapper::class));
        assertType(GenericsFixturePaymentWrapper::class, $wrapped);

        // Re-targeting the canonical class flows into send().
        $refunds = $client->send((new DynamicGetRequest('payments/tr_x/refunds'))->hydrateInto(RefundCollection::class));
        assertType(RefundCollection::class, $refunds);

        // Both jobs: target first, wrapper last; the wrapper is what comes back.
        $both = $client->send(
            (new DynamicGetRequest('payments/tr_x/refunds'))
                ->hydrateInto(RefundCollection::class)
                ->wrapInto(GenericsFixturePaymentWrapper::class)
        );
        assertType(GenericsFixturePaymentWrapper::class, $both);
    }
}

final class GenericsFixturePaymentWrapper extends ResourceWrapper
{
    public static function fromResource($resource): self
    {
        return (new self)->wrap($resource);
    }
}
