<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\EmailDetails;
use Mollie\Api\Http\Data\PaymentDetails;
use Mollie\Api\Http\Requests\CreateSalesInvoiceRequest;

class CreateSalesInvoiceRequestFactory extends RequestFactory
{
    /**
     * Create a new CreateSalesInvoicePayload instance.
     */
    public function create(): CreateSalesInvoiceRequest
    {
        return new CreateSalesInvoiceRequest(
            $this->payload('currency'),
            $this->payload('status'),
            $this->payload('vatScheme'),
            $this->payload('vatMode'),
            $this->payload('paymentTerm'),
            $this->payload('recipientIdentifier'),
            RecipientFactory::new($this->payload('recipient'))->create(),
            $this
                ->transformFromPayload(
                    'lines',
                    fn (array $items) => InvoiceLineCollectionFactory::new($items)->create()
                ),
            $this->payload('profileId'),
            $this->payload('memo'),
            $this->transformFromPayload('paymentDetails', fn (array $data) => PaymentDetails::fromArray($data)),
            $this->transformFromPayload('emailDetails', fn (array $data) => EmailDetails::fromArray($data)),
            $this->payload('webhookUrl'),
            $this->transformFromPayload('discount', fn (array $data) => Discount::fromArray($data))
        );
    }
}
