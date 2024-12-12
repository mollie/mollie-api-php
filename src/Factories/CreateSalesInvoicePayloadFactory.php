<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\CreateSalesInvoicePayload;
use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\EmailDetails;
use Mollie\Api\Http\Data\PaymentDetails;

class CreateSalesInvoicePayloadFactory extends Factory
{
    /**
     * Create a new CreateSalesInvoicePayload instance.
     */
    public function create(): CreateSalesInvoicePayload
    {
        return new CreateSalesInvoicePayload(
            $this->get('currency'),
            $this->get('status'),
            $this->get('vatScheme'),
            $this->get('vatMode'),
            $this->get('paymentTerm'),
            $this->get('recipientIdentifier'),
            RecipientFactory::new($this->get('recipient'))->create(),
            $this
                ->mapIfNotNull(
                    'lines',
                    fn (array $items) => InvoiceLineCollectionFactory::new($items)->create()
                ),
            $this->get('profileId'),
            $this->get('memo'),
            $this->mapIfNotNull('paymentDetails', fn (array $data) => PaymentDetails::fromArray($data)),
            $this->mapIfNotNull('emailDetails', fn (array $data) => EmailDetails::fromArray($data)),
            $this->get('webhookUrl'),
            $this->mapIfNotNull('discount', fn (array $data) => Discount::fromArray($data))
        );
    }
}
