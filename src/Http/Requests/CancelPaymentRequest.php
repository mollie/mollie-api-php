<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Rules\Id;
use Mollie\Api\Types\Method;

class CancelPaymentRequest extends SimpleRequest
{
    protected static string $method = Method::DELETE;

    public static string $targetResourceClass = Payment::class;

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
