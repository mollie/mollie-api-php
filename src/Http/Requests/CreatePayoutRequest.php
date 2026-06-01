<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Payout;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePayoutRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Payout::class;

    private string $balanceId;

    private ?Money $amount;

    private ?string $description;

    public function __construct(
        string $balanceId,
        ?Money $amount = null,
        ?string $description = null
    ) {
        $this->balanceId = $balanceId;
        $this->amount = $amount;
        $this->description = $description;
    }

    protected function defaultPayload(): array
    {
        return [
            'balanceId' => $this->balanceId,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payouts';
    }
}
