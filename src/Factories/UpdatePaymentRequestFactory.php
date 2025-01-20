<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Metadata;
use Mollie\Api\Http\Data\UpdatePaymentPayload;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Utils\Utility;

class UpdatePaymentRequestFactory extends Factory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function new(string $id): self
    {
        return new self($id);
    }

    public function create(): UpdatePaymentRequest
    {
        return new UpdatePaymentRequest(
            $this->id,
            $this->payload('description'),
            $this->payload('redirectUrl'),
            $this->payload('cancelUrl'),
            $this->payload('webhookUrl'),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->payload('method'),
            $this->payload('locale'),
            $this->payload('restrictPaymentMethodsToCountry'),
            $this->payload('additional') ?? Utility::filterByProperties(UpdatePaymentPayload::class, $this->payload),
        );
    }
}
