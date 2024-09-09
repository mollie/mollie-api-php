<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Payload\CreatePaymentPayload;
use Mollie\Api\Http\Query\CreatePaymentQuery;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentRequest extends Request implements HasPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Payment::class;

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
