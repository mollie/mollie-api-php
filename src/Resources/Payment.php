<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Payment extends BaseResource implements EmbeddedResourcesContract
{
    use HasMode;

    public string $id;

    /**
     * Mode of the payment, either "live" or "test" depending on the API Key that was used.
     */
    public string $mode;

    /**
     * Amount object containing the value and currency.
     */
    public Money $amount;

    public ?Money $settlementAmount = null;

    public ?Money $amountRefunded = null;

    public ?Money $amountRemaining = null;

    public ?Money $amountChargedBack = null;

    /**
     * Description of the payment that is shown to the customer during the payment,
     * and possibly on the bank or credit card statement.
     */
    public string $description;

    public PaymentMethod|string|null $method = null;

    /**
     * The status of the payment. Enum case if recognised, raw string for forward-compat.
     */
    public PaymentStatus|string $status = PaymentStatus::Open;

    /**
     * The reason for the status of the payment.
     *
     * @var \stdClass|null
     */
    public $statusReason;

    public ?string $createdAt = null;

    public ?string $paidAt = null;

    public ?string $canceledAt = null;

    public ?string $expiresAt = null;

    public ?string $failedAt = null;

    /**
     * Only used for banktransfer method. ISO-8601 due date.
     */
    public ?string $dueDate = null;

    /**
     * @deprecated 2024-06-01 The billingEmail field is deprecated. Use the "billingAddress" field instead.
     */
    public ?string $billingEmail = null;

    /**
     * The profile ID this payment belongs to.
     */
    public string $profileId;

    public SequenceType|string|null $sequenceType = null;

    public ?string $redirectUrl = null;

    public ?string $cancelUrl = null;

    public ?string $webhookUrl = null;

    public ?string $mandateId = null;

    public ?string $subscriptionId = null;

    public ?string $orderId = null;

    /**
     * The lines contain the actual items the customer bought.
     *
     * @var array|null
     */
    public ?array $lines = null;

    public ?Address $billingAddress = null;

    public ?Address $shippingAddress = null;

    public ?string $settlementId = null;

    public ?string $locale = null;

    /**
     * @var \stdClass|array|null
     */
    public $metadata;

    /**
     * Details of a successfully paid payment.
     *
     * @var \stdClass|null
     */
    public $details;

    public ?string $restrictPaymentMethodsToCountry = null;

    /**
     * @var \stdClass|null
     */
    public $_links;

    /**
     * @var \stdClass|null
     */
    public $_embedded;

    public ?bool $isCancelable = null;

    public ?Money $amountCaptured = null;

    public ?string $captureMode = null;

    public ?string $captureDelay = null;

    public ?string $captureBefore = null;

    /**
     * The application fee (nested object: { amount: {value,currency}, description }).
     *
     * @var \stdClass|null
     */
    public $applicationFee;

    /**
     * Routing configuration for split payments.
     *
     * @var array|null
     */
    public ?array $routing = null;

    public ?string $authorizedAt = null;

    public ?string $expiredAt = null;

    public ?string $customerId = null;

    public ?string $countryCode = null;

    public function getEmbeddedResourcesMap(): array
    {
        return [
            'captures' => CaptureCollection::class,
            'refunds' => RefundCollection::class,
            'chargebacks' => ChargebackCollection::class,
        ];
    }

    public function isCanceled(): bool
    {
        return $this->status === PaymentStatus::Canceled;
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentStatus::Expired;
    }

    public function isOpen(): bool
    {
        return $this->status === PaymentStatus::Open;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function isAuthorized(): bool
    {
        return $this->status === PaymentStatus::Authorized;
    }

    public function isPaid(): bool
    {
        return ! empty($this->paidAt);
    }

    public function hasRefunds(): bool
    {
        return ! empty($this->_links->refunds);
    }

    public function hasChargebacks(): bool
    {
        return ! empty($this->_links->chargebacks);
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::Failed;
    }

    public function hasSequenceTypeFirst(): bool
    {
        return $this->sequenceType === SequenceType::First;
    }

    public function hasSequenceTypeRecurring(): bool
    {
        return $this->sequenceType === SequenceType::Recurring;
    }

    public function getCheckoutUrl(): ?string
    {
        if (empty($this->_links->checkout)) {
            return null;
        }

        return $this->_links->checkout->href;
    }

    public function getMobileAppCheckoutUrl(): ?string
    {
        if (empty($this->_links->mobileAppCheckout)) {
            return null;
        }

        return $this->_links->mobileAppCheckout->href;
    }

    public function canBeRefunded(): bool
    {
        return $this->amountRemaining !== null;
    }

    public function canBePartiallyRefunded(): bool
    {
        return $this->canBeRefunded();
    }

    public function getAmountRefunded(): float
    {
        if ($this->amountRefunded) {
            return (float) $this->amountRefunded->value;
        }

        return 0.0;
    }

    public function getAmountRemaining(): float
    {
        if ($this->amountRemaining) {
            return (float) $this->amountRemaining->value;
        }

        return 0.0;
    }

    public function getAmountChargedBack(): float
    {
        if ($this->amountChargedBack) {
            return (float) $this->amountChargedBack->value;
        }

        return 0.0;
    }

    public function hasSplitPayments(): bool
    {
        return ! empty($this->routing);
    }

    /**
     * @throws ApiException
     */
    public function refunds(): RefundCollection
    {
        if (! isset($this->_links->refunds->href)) {
            return $this->listRefunds();
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->refunds->href))->setHydratableResource(RefundCollection::class));
    }

    /**
     * @throws ApiException
     */
    public function getRefund(string $refundId, array $parameters = []): Refund
    {
        return $this->connector->paymentRefunds->getFor($this, $refundId, $parameters, $this->isInTestmode());
    }

    /**
     * @throws ApiException
     */
    public function listRefunds(array $parameters = []): RefundCollection
    {
        return $this
            ->connector
            ->paymentRefunds
            ->pageFor(
                $this,
                null,
                null,
                $this->withMode($parameters),
            );
    }

    /**
     * @throws ApiException
     */
    public function captures(): CaptureCollection
    {
        if (! isset($this->_links->captures->href)) {
            return $this->connector->paymentCaptures->pageFor($this, [], $this->isInTestmode());
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->captures->href))->setHydratableResource(CaptureCollection::class));
    }

    /**
     * @throws ApiException
     */
    public function getCapture(string $captureId, array $parameters = []): Capture
    {
        return $this->connector->paymentCaptures->getFor(
            $this,
            $captureId,
            $parameters,
            $this->isInTestmode()
        );
    }

    /**
     * @throws ApiException
     */
    public function chargebacks(): ChargebackCollection
    {
        if (! isset($this->_links->chargebacks->href)) {
            return $this->connector->paymentChargebacks->pageFor($this, $this->withMode());
        }

        return $this
            ->connector
            ->send((new DynamicGetRequest($this->_links->chargebacks->href))->setHydratableResource(ChargebackCollection::class));
    }

    /**
     * @throws ApiException
     */
    public function getChargeback(string $chargebackId, array $parameters = []): Chargeback
    {
        return $this->connector->paymentChargebacks->getFor(
            $this,
            $chargebackId,
            $parameters,
            $this->isInTestmode()
        );
    }

    /**
     * @throws ApiException
     */
    public function refund(array $data): Refund
    {
        return $this->connector->paymentRefunds->createFor($this, $data);
    }

    /**
     * @throws ApiException
     */
    public function update(): Payment
    {
        $additional = [];
        if ($this->method === PaymentMethod::Banktransfer || $this->method === PaymentMethod::Banktransfer->value) {
            $additional['dueDate'] = $this->dueDate;
        }

        $method = $this->method instanceof PaymentMethod ? $this->method->value : $this->method;

        $updateRequest = (new UpdatePaymentRequest(
            $this->id,
            $this->description,
            $this->redirectUrl,
            $this->cancelUrl,
            $this->webhookUrl,
            (array) $this->metadata,
            $method,
            $this->locale,
            $this->restrictPaymentMethodsToCountry,
            $additional
        ))->test($this->isInTestmode());

        /** @var Payment */
        return $this
            ->connector
            ->send($updateRequest);
    }

    public function getAmountCaptured(): float
    {
        if ($this->amountCaptured) {
            return (float) $this->amountCaptured->value;
        }

        return 0.0;
    }

    public function getSettlementAmount(): float
    {
        if ($this->settlementAmount) {
            return (float) $this->settlementAmount->value;
        }

        return 0.0;
    }

    public function getApplicationFeeAmount(): float
    {
        if ($this->applicationFee) {
            return (float) $this->applicationFee->amount->value;
        }

        return 0.0;
    }
}
