<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\EmailDetails;
use Mollie\Api\Http\Data\InvoiceLine;
use Mollie\Api\Http\Data\PaymentDetails;
use Mollie\Api\Http\Data\Recipient;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Types\PaymentTerm;
use Mollie\Api\Types\VatMode;
use Mollie\Api\Types\VatScheme;

class CreateSalesInvoiceRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected $hydratableResource = SalesInvoice::class;

    private string $currency;

    private string $status;

    private string $vatScheme = VatScheme::STANDARD;

    private string $vatMode = VatMode::EXCLUSIVE;

    private string $paymentTerm = PaymentTerm::DAYS_30;

    public string $recipientIdentifier;

    public Recipient $recipient;

    /**
     * @var DataCollection<InvoiceLine>
     */
    public DataCollection $lines;

    public ?string $profileId;

    public ?string $memo;

    public ?PaymentDetails $paymentDetails;

    public ?EmailDetails $emailDetails;

    public ?string $webhookUrl;

    public ?Discount $discount;

    public ?string $customerId;

    public ?string $mandateId;

    public ?bool $isEInvoice;

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
        ?string $customerId = null,
        ?string $mandateId = null,
        ?bool $isEInvoice = null
    ) {
        $this->currency = $currency;
        $this->status = $status;
        $this->vatScheme = $vatScheme;
        $this->vatMode = $vatMode;
        $this->paymentTerm = $paymentTerm;
        $this->recipientIdentifier = $recipientIdentifier;
        $this->recipient = $recipient;
        $this->lines = $lines;
        $this->profileId = $profileId;
        $this->memo = $memo;
        $this->paymentDetails = $paymentDetails;
        $this->emailDetails = $emailDetails;
        $this->webhookUrl = $webhookUrl;
        $this->discount = $discount;
        $this->customerId = $customerId;
        $this->mandateId = $mandateId;
        $this->isEInvoice = $isEInvoice;
    }

    public function defaultPayload(): array
    {
        return [
            'currency' => $this->currency,
            'status' => $this->status,
            'vatScheme' => $this->vatScheme,
            'vatMode' => $this->vatMode,
            'paymentTerm' => $this->paymentTerm,
            'recipientIdentifier' => $this->recipientIdentifier,
            'recipient' => $this->recipient,
            'lines' => $this->lines,
            'profileId' => $this->profileId,
            'memo' => $this->memo,
            'paymentDetails' => $this->paymentDetails,
            'emailDetails' => $this->emailDetails,
            'webhookUrl' => $this->webhookUrl,
            'discount' => $this->discount,
            'customerId' => $this->customerId,
            'mandateId' => $this->mandateId,
            'isEInvoice' => $this->isEInvoice,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'sales-invoices';
    }
}
