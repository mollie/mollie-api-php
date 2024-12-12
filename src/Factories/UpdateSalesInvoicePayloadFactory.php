<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\EmailDetails;
use Mollie\Api\Http\Data\PaymentDetails;
use Mollie\Api\Http\Data\UpdateSalesInvoicePayload;

class UpdateSalesInvoicePayloadFactory extends Factory
{
    /**
     * Create a new UpdateSalesInvoicePayload instance.
     */
    public function create(): UpdateSalesInvoicePayload
    {
        return new UpdateSalesInvoicePayload(
            $this->get('status'),
            $this->get('recipientIdentifier'),
            $this->get('paymentTerm'),
            $this->get('memo'),
            $this->mapIfNotNull('paymentDetails', fn (array $data) => PaymentDetails::fromArray($data)),
            $this->mapIfNotNull('emailDetails', fn (array $data) => EmailDetails::fromArray($data)),
            $this->mapIfNotNull('recipient', fn (array $data) => RecipientFactory::new($data)->create()),
            $this
                ->mapIfNotNull(
                    'lines',
                    fn (array $items) => InvoiceLineCollectionFactory::new($items)->create()
                ),
            $this->get('webhookUrl'),
            $this->mapIfNotNull('discount', fn (array $data) => Discount::fromArray($data))
        );
    }
}
