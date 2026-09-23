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
 * @see https://docs.mollie.com/reference/create-sales-invoice
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\SalesInvoice>
 */
class CreateSalesInvoiceRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::POST;

    protected ?string $hydratableResource = SalesInvoice::class;

    public function __construct(
        private string $currency,
        private string $status,
        private string $vatScheme,
        private string $vatMode,
        private string $paymentTerm,
        private string $recipientIdentifier,
        private Recipient $recipient,
        private DataCollection $lines,
        private ?string $profileId = null,
        private ?string $memo = null,
        private ?PaymentDetails $paymentDetails = null,
        private ?EmailDetails $emailDetails = null,
        private ?string $webhookUrl = null,
        private ?Discount $discount = null,
        private ?string $customerId = null,
        private ?string $mandateId = null,
        private ?bool $isEInvoice = null,
    ) {
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
