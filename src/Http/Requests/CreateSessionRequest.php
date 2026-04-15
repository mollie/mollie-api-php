<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Session;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\CheckoutFlow;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Session>
 */
class CreateSessionRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected ?string $hydratableResource = Session::class;

    private string $paymentMethod;

    private string $checkoutFlow;

    public function __construct(
        private string $redirectUrl,
        private string $cancelUrl,
        private Money $amount,
        private string $description,
        string $method,
        ?string $checkoutFlow = null,
    ) {
        $this->paymentMethod = $method;
        $this->checkoutFlow = $checkoutFlow ?? CheckoutFlow::Express->value;
    }

    protected function defaultPayload(): array
    {
        return [
            'redirectUrl' => $this->redirectUrl,
            'cancelUrl' => $this->cancelUrl,
            'amount' => $this->amount,
            'description' => $this->description,
            'method' => $this->paymentMethod,
            'checkoutFlow' => $this->checkoutFlow,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'sessions';
    }
}
