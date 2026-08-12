<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Discount;
use Mollie\Api\Http\Data\EmailDetails;
use Mollie\Api\Http\Data\PaymentDetails;
use Mollie\Api\Http\Data\Recipient;
use Mollie\Api\Resources\SalesInvoice;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\SalesInvoice>
 */
class UpdateSalesInvoiceRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    protected ?string $hydratableResource = SalesInvoice::class;

    public function __construct(
        private string $id,
        private ?string $status = null,
        private ?string $recipientIdentifier = null,
        private ?string $memo = null,
        private ?string $paymentTerm = null,
        private ?PaymentDetails $paymentDetails = null,
        private ?EmailDetails $emailDetails = null,
        private ?Recipient $recipient = null,
        private ?DataCollection $lines = null,
        private ?string $webhookUrl = null,
        private ?Discount $discount = null,
        private ?bool $isEInvoice = null,
    ) {
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
            'isEInvoice' => $this->isEInvoice,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "sales-invoices/{$this->id}";
    }
}
