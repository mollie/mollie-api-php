<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Http\Request;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Rules\Id;
use Mollie\Api\Types\Method;

class CancelPaymentRequest extends Request
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Payment::class;

    private string $id;

    private bool $testmode;

    public function __construct(string $id, bool $testmode = false)
    {
        $this->id = $id;
        $this->testmode = $testmode;
    }

    protected function defaultQuery(): array
    {
        return [
            'testmode' => $this->testmode,
        ];
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
