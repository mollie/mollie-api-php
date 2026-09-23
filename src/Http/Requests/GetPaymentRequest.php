<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentQuery;
use Mollie\Api\Utils\Arr;

/**
 * @see https://docs.mollie.com/reference/v2/payments-api/get-payment
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Payment>
 */
class GetPaymentRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Payment::class;

    public function __construct(
        private string $id,
        private bool $embedCaptures = false,
        private bool $embedRefunds = false,
        private bool $embedChargebacks = false,
        private bool $includeQrCode = false,
        private bool $includeRemainderDetails = false,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'embed' => Arr::join([
                $this->embedCaptures ? PaymentQuery::EMBED_CAPTURES : null,
                $this->embedRefunds ? PaymentQuery::EMBED_REFUNDS : null,
                $this->embedChargebacks ? PaymentQuery::EMBED_CHARGEBACKS : null,
            ]),
            'include' => Arr::join([
                $this->includeQrCode ? PaymentQuery::INCLUDE_QR_CODE : null,
                $this->includeRemainderDetails ? PaymentQuery::INCLUDE_REMAINDER_DETAILS : null,
            ]),
        ];
    }

    /**
     * Resolve the resource path.
     */
    public function resolveResourcePath(): string
    {
        return "payments/{$this->id}";
    }
}
