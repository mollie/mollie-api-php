<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Requests\CancelPaymentRefundRequest;
use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\RefundStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Refund extends BaseResource
{
    use HasMode;

    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;

    /**
     * Mode of the refund, either "live" or "test".
     *
     * @var string
     */
    public $mode;

    /**
     * The $amount that was refunded.
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * UTC datetime the payment was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string
     */
    public $createdAt;

    /**
     * The refund's description, if available.
     *
     * @var string|null
     */
    public $description;

    /**
     * The payment id that was refunded.
     *
     * @var string
     */
    public $paymentId;

    /**
     * The order id that was refunded.
     *
     * @var string|null
     */
    public $orderId;

    /**
     * The order lines contain the actual things the customer ordered.
     * The lines will show the quantity, discountAmount, vatAmount and totalAmount
     * refunded.
     *
     * @var array|object[]|null
     */
    public $lines;

    /**
     * The settlement amount
     *
     * @var \stdClass
     */
    public $settlementAmount;

    /**
     * The refund status
     *
     * @var string
     */
    public $status;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * An object containing information relevant to a refund issued for a split payment.
     *
     * @var array|object[]|null
     */
    public $routingReversal;

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
        return $this->status === RefundStatus::QUEUED;
    }

    /**
     * Is this refund pending?
     */
    public function isPending(): bool
    {
        return $this->status === RefundStatus::PENDING;
    }

    /**
     * Is this refund processing?
     */
    public function isProcessing(): bool
    {
        return $this->status === RefundStatus::PROCESSING;
    }

    /**
     * Is this refund transferred to consumer?
     */
    public function isTransferred(): bool
    {
        return $this->status === RefundStatus::REFUNDED;
    }

    /**
     * Is this refund failed?
     */
    public function isFailed(): bool
    {
        return $this->status === RefundStatus::FAILED;
    }

    /**
     * Is this refund canceled?
     */
    public function isCanceled(): bool
    {
        return $this->status === RefundStatus::CANCELED;
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
