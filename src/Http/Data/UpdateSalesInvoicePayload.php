<?php

namespace Mollie\Api\Http\Data;

class UpdateSalesInvoicePayload extends Data
{
    public string $status;

    public ?string $memo;

    public ?string $paymentTerm;

    public ?PaymentDetails $paymentDetails;

    public ?EmailDetails $emailDetails;

    public string $recipientIdentifier;

    public ?Recipient $recipient;

    /**
     * @var DataCollection<InvoiceLine>
     */
    public ?DataCollection $lines;

    public ?string $webhookUrl;

    public ?Discount $discount;

    public function __construct(
        string $status,
        string $recipientIdentifier,
        ?string $paymentTerm = null,
        ?string $memo = null,
        ?PaymentDetails $paymentDetails = null,
        ?EmailDetails $emailDetails = null,
        ?Recipient $recipient = null,
        ?DataCollection $lines = null,
        ?string $webhookUrl = null,
        ?Discount $discount = null
    ) {
        $this->status = $status;
        $this->paymentTerm = $paymentTerm;
        $this->recipientIdentifier = $recipientIdentifier;
        $this->memo = $memo;
        $this->paymentDetails = $paymentDetails;
        $this->emailDetails = $emailDetails;
        $this->recipient = $recipient;
        $this->lines = $lines;
        $this->webhookUrl = $webhookUrl;
        $this->discount = $discount;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'memo' => $this->memo,
            'paymentTerm' => $this->paymentTerm,
            'paymentDetails' => $this->paymentDetails,
            'emailDetails' => $this->emailDetails,
            'recipientIdentifier' => $this->recipientIdentifier,
            'recipient' => $this->recipient,
            'lines' => $this->lines,
            'webhookUrl' => $this->webhookUrl,
            'discount' => $this->discount,
        ];
    }
}
