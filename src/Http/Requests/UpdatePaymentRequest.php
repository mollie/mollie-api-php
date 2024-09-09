<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Payload\UpdatePaymentPayload;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Rules\Id;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdatePaymentRequest extends Request implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Payment::class;

    private string $id;

    private UpdatePaymentPayload $payload;

    public function __construct(string $id, UpdatePaymentPayload $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function rules(): array
    {
        return [
            'id' => Id::startsWithPrefix(Payment::$resourceIdPrefix),
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->id}";
    }
}
