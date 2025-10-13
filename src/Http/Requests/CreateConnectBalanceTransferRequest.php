<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\ConnectBalanceTransfer;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreateConnectBalanceTransferRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = ConnectBalanceTransfer::class;

    private Money $amount;

    private string $description;

    private string $sourceBalanceId;

    private string $destinationBalanceId;

    public function __construct(
        Money $amount,
        string $description,
        string $sourceBalanceId,
        string $destinationBalanceId
    ) {
        $this->amount = $amount;
        $this->description = $description;
        $this->sourceBalanceId = $sourceBalanceId;
        $this->destinationBalanceId = $destinationBalanceId;
    }

    protected function defaultPayload(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'source' => [
                'balanceId' => $this->sourceBalanceId,
            ],
            'destination' => [
                'balanceId' => $this->destinationBalanceId,
            ],
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'connect/balance-transfers';
    }
}
