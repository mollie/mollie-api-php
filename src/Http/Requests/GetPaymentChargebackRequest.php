<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentIncludesQuery;

/**
 * @see https://docs.mollie.com/reference/get-chargeback
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Chargeback>
 */
class GetPaymentChargebackRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Chargeback::class;

    public function __construct(
        private string $paymentId,
        private string $chargebackId,
        private bool $includePayment = false,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : null,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/chargebacks/{$this->chargebackId}";
    }
}
