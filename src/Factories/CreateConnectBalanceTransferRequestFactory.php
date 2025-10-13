<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\CreateConnectBalanceTransferRequest;

class CreateConnectBalanceTransferRequestFactory extends RequestFactory
{
    public function create(): CreateConnectBalanceTransferRequest
    {
        return new CreateConnectBalanceTransferRequest(
            $this->transformFromPayload('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $this->payload('description'),
            $this->payload('source.balanceId'),
            $this->payload('destination.balanceId')
        );
    }
}
