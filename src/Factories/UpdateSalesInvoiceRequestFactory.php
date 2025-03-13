<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\EmailDetails;
use Mollie\Api\Http\Data\PaymentDetails;
use Mollie\Api\Http\Requests\UpdateSalesInvoiceRequest;

class UpdateSalesInvoiceRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): UpdateSalesInvoiceRequest
    {
        return new UpdateSalesInvoiceRequest(
            $this->id,
            $this->payload('status'),
            $this->payload('recipientIdentifier'),
            $this->payload('paymentTerm'),
            $this->payload('memo'),
            $this->transformFromPayload('paymentDetails', fn ($data) => PaymentDetails::fromArray($data)),
            $this->transformFromPayload('emailDetails', fn ($data) => EmailDetails::fromArray($data)),
            $this->transformFromPayload('recipient', fn ($data) => RecipientFactory::new($data)->create()),
            $this
                ->transformFromPayload(
                    'lines',
                    fn (array $items) => InvoiceLineCollectionFactory::new($items)->create()
                ),
            $this->payload('webhookUrl'),
            $this->transformFromPayload('discount', fn ($data) => Discount::fromArray($data))
        );
    }
}
