<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Data\CreatePaymentQuery;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInQuery
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Payment::class;

    private CreatePaymentPayload $payload;

    private ?CreatePaymentQuery $query = null;

    public function __construct(CreatePaymentPayload $payload, ?CreatePaymentQuery $query = null)
    {
        $this->payload = $payload;
        $this->query = $query;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    protected function defaultQuery(): array
    {
        return $this->query ? $this->query->toArray() : [];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payments';
    }
}
