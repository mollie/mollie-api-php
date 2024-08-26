<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Payment;

class CreatePaymentRequest extends JsonPostRequest
{
    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Payment::class;

    /**
     * The request filters.
     */
    protected array $include;

    public function __construct(array $body, array $include = [])
    {
        $this->body = $body;
        $this->include = $include;
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payments';
    }
}
