<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateCustomerPaymentRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
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

    protected string $customerId;

    protected string $profileId;

    public function __construct(string $customerId, ?string $profileId = null)
    {
        $this->customerId = $customerId;
        $this->profileId = $profileId;
    }

    protected function defaultPayload(): array
    {
        return [
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/payments";
    }
}
