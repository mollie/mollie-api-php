<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\TransferParty;
use Mollie\Api\Http\Requests\CreateConnectBalanceTransferRequest;

class CreateConnectBalanceTransferRequestFactory extends RequestFactory
{
    public function create(): CreateConnectBalanceTransferRequest
    {
        return new CreateConnectBalanceTransferRequest(
            $this->transformFromPayload('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $this->payload('description'),
            $this->transformFromPayload('source', fn ($item) => TransferParty::fromArray($item)),
            $this->transformFromPayload('destination', fn ($item) => TransferParty::fromArray($item)),
            $this->payload('category'),
            $this->payload('metadata')
        );
    }
}
