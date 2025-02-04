<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\EmailDetails;
use Mollie\Api\Http\Data\InvoiceLine;
use Mollie\Api\Http\Data\PaymentDetails;
use Mollie\Api\Http\Data\Recipient;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdateSalesInvoiceRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    protected $hydratableResource = SalesInvoice::class;

    private string $id;

    private string $status;

    private string $recipientIdentifier;

    private ?string $memo;

    private ?string $paymentTerm;

    private ?PaymentDetails $paymentDetails;

    private ?EmailDetails $emailDetails;

    private ?Recipient $recipient;

    /**
     * @var DataCollection<InvoiceLine>
     */
    private ?DataCollection $lines;

    private ?string $webhookUrl;

    private ?Discount $discount;

    public function __construct(
        string $id,
        string $status,
        string $recipientIdentifier,
        ?string $memo = null,
        ?string $paymentTerm = null,
        ?PaymentDetails $paymentDetails = null,
        ?EmailDetails $emailDetails = null,
        ?Recipient $recipient = null,
        ?DataCollection $lines = null,
        ?string $webhookUrl = null,
        ?Discount $discount = null
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->recipientIdentifier = $recipientIdentifier;
        $this->memo = $memo;
        $this->paymentTerm = $paymentTerm;
        $this->paymentDetails = $paymentDetails;
        $this->emailDetails = $emailDetails;
        $this->recipient = $recipient;
        $this->lines = $lines;
        $this->webhookUrl = $webhookUrl;
        $this->discount = $discount;
    }

    public function defaultPayload(): array
    {
        return [
            'status' => $this->status,
            'recipientIdentifier' => $this->recipientIdentifier,
            'memo' => $this->memo,
            'paymentTerm' => $this->paymentTerm,
            'paymentDetails' => $this->paymentDetails,
            'emailDetails' => $this->emailDetails,
            'recipient' => $this->recipient,
            'lines' => $this->lines,
            'webhookUrl' => $this->webhookUrl,
            'discount' => $this->discount,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "sales-invoices/{$this->id}";
    }
}
