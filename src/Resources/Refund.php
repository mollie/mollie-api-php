<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CancelPaymentRefundRequest;
use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\RefundStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Refund extends BaseResource
{
    use HasMode;

    public string $id;

    /**
     * Mode of the refund, either "live" or "test".
     */
    public string $mode;

    /**
     * The amount that was refunded.
     */
    public Money $amount;

    /**
     * UTC datetime the refund was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     */
    public string $createdAt;

    /**
     * The refund's description, if available.
     */
    public ?string $description = null;

    /**
     * The payment id that was refunded.
     */
    public string $paymentId;

    /**
     * The order id that was refunded.
     */
    public ?string $orderId = null;

    /**
     * The order lines contain the actual things the customer ordered.
     * The lines will show the quantity, discountAmount, vatAmount and totalAmount
     * refunded.
     *
     * @var array|null
     */
    public ?array $lines = null;

    /**
     * The settlement amount.
     */
    public ?Money $settlementAmount = null;

    /**
     * The refund status. Enum case if recognised, raw string for forward-compat.
     */
    public RefundStatus|string|null $status = null;

    /**
     * @var \stdClass|null
     */
    public $_links;

    /**
     * An object containing information relevant to a refund issued for a split payment.
     *
     * @var array|null
     */
    public ?array $routingReversal = null;

    /**
     * @var \stdClass|null
     */
    public $metadata;

    public function canBeCanceled(): bool
    {
        return $this->isQueued() || $this->isPending();
    }

    /**
     * Is this refund queued?
     */
    public function isQueued(): bool
    {
        return $this->status === RefundStatus::Queued;
    }

    /**
     * Is this refund pending?
     */
    public function isPending(): bool
    {
        return $this->status === RefundStatus::Pending;
    }

    /**
     * Is this refund processing?
     */
    public function isProcessing(): bool
    {
        return $this->status === RefundStatus::Processing;
    }

    /**
     * Is this refund transferred to consumer?
     */
    public function isTransferred(): bool
    {
        return $this->status === RefundStatus::Refunded;
    }

    /**
     * Is this refund failed?
     */
    public function isFailed(): bool
    {
        return $this->status === RefundStatus::Failed;
    }

    /**
     * Is this refund canceled?
     */
    public function isCanceled(): bool
    {
        return $this->status === RefundStatus::Canceled;
    }

    /**
     * Cancel the refund.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel(): void
    {
        $this
            ->connector
            ->send((new CancelPaymentRefundRequest(
                $this->paymentId,
                $this->id
            ))->test($this->isInTestmode()));
    }
}
