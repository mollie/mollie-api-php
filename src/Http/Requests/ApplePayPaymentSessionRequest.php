<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\AnyResource>
 */
class ApplePayPaymentSessionRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected ?string $hydratableResource = AnyResource::class;

    public function __construct(
        private string $domain,
        private string $validationUrl,
        private ?string $profileId = null,
    )
    {
    }

    public function defaultPayload(): array
    {
        return [
            'domain' => $this->domain,
            'validationUrl' => $this->validationUrl,
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'wallets/applepay/sessions';
    }
}
