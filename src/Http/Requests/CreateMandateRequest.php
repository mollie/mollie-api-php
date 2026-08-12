<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Date;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/mandates-api/create-mandate
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Mandate>
 */
class CreateMandateRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Mandate::class;

    private string $paymentMethod;

    public function __construct(
        private string $customerId,
        string $method,
        private string $consumerName,
        private ?string $consumerAccount = null,
        private ?string $consumerBic = null,
        private ?string $consumerEmail = null,
        private Date|DateTimeInterface|null $signatureDate = null,
        private ?string $mandateReference = null,
        private ?string $paypalBillingAgreementId = null,
    ) {
        $this->paymentMethod = $method;
    }

    protected function defaultPayload(): array
    {
        return [
            'method' => $this->paymentMethod,
            'consumerName' => $this->consumerName,
            'consumerAccount' => $this->consumerAccount,
            'consumerBic' => $this->consumerBic,
            'consumerEmail' => $this->consumerEmail,
            'signatureDate' => $this->signatureDate,
            'mandateReference' => $this->mandateReference,
            'paypalBillingAgreementId' => $this->paypalBillingAgreementId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/mandates";
    }
}
