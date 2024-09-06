<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Payload\UpdatePayment;
use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Rules\Id;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdatePaymentRequest extends Request
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    private string $id;

    private UpdatePayment $payload;

    public function __construct(string $id, UpdatePayment $payload)
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
