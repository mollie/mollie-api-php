<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Types\PaymentTerm;
use Mollie\Api\Types\VatMode;
use Mollie\Api\Types\VatScheme;

class CreateSalesInvoicePayload extends DataBag
{
    public ?string $profileId;

    public string $currency;

    public string $status;

    public string $vatScheme = VatScheme::STANDARD;

    public string $vatMode = VatMode::EXCLUSIVE;

    public ?string $memo;

    public string $paymentTerm = PaymentTerm::DAYS_30;

    public ?PaymentDetails $paymentDetails;

    public ?EmailDetails $emailDetails;

    public string $recipientIdentifier;

    public Recipient $recipient;

    /**
     * @var DataCollection<InvoiceLine>
     */
    public DataCollection $lines;

    public ?string $webhookUrl;

    public ?Discount $discount;

    public function __construct(
        string $currency,
        string $status,
        string $vatScheme,
        string $vatMode,
        string $paymentTerm,
        string $recipientIdentifier,
        Recipient $recipient,
        DataCollection $lines,
        ?string $profileId = null,
        ?string $memo = null,
        ?PaymentDetails $paymentDetails = null,
        ?EmailDetails $emailDetails = null,
        ?string $webhookUrl = null,
        ?Discount $discount = null,
    ) {
        $this->profileId = $profileId;
        $this->currency = $currency;
        $this->status = $status;
        $this->vatScheme = $vatScheme;
        $this->vatMode = $vatMode;
        $this->memo = $memo;
        $this->paymentTerm = $paymentTerm;
        $this->paymentDetails = $paymentDetails;
        $this->emailDetails = $emailDetails;
        $this->recipientIdentifier = $recipientIdentifier;
        $this->recipient = $recipient;
        $this->lines = $lines;
        $this->webhookUrl = $webhookUrl;
        $this->discount = $discount;
    }

    public function data(): array
    {
        return [
            'profileId' => $this->profileId,
            'currency' => $this->currency,
            'status' => $this->status,
            'vatScheme' => $this->vatScheme,
            'vatMode' => $this->vatMode,
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
