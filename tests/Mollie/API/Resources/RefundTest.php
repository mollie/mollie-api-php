<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\Endpoints\PaymentRefundEndpoint;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Types\RefundStatus;

class RefundTest extends \PHPUnit\Framework\TestCase
{
    public function testCancelRefundUsingOauthTestmode()
    {
        $endpoint = $this->createMock(PaymentRefundEndpoint::class);
        $endpoint->expects($this->once())
            ->method('cancelForId')
            ->with(
                $this->equalTo('tr_abc'),  // paymentId
                $this->equalTo('re_123'),   // refundId
                $this->equalTo(['testmode' => true])
            );

        $apiClient = $this->createMock(MollieApiClient::class);
        $apiClient->paymentRefunds = $endpoint;
        $apiClient->expects($this->once())
            ->method('usesOAuth')
            ->willReturn(true);

        $refund = new Refund($apiClient);
        $refund->id = 're_123';
        $refund->paymentId = 'tr_abc';
        $refund->mode = 'test';

        $response = $refund->cancel();

        $this->assertNull($response);
    }

    public function testCancelRefundUsingOauthLivemode()
    {
        $endpoint = $this->createMock(PaymentRefundEndpoint::class);
        $endpoint->expects($this->once())
            ->method('cancelForId')
            ->with(
                $this->equalTo('tr_abc'),  // paymentId
                $this->equalTo('re_123'),   // refundId
                $this->equalTo(['testmode' => false])
            );

        $apiClient = $this->createMock(MollieApiClient::class);
        $apiClient->paymentRefunds = $endpoint;
        $apiClient->expects($this->once())
            ->method('usesOAuth')
            ->willReturn(true);

        $refund = new Refund($apiClient);
        $refund->id = 're_123';
        $refund->paymentId = 'tr_abc';
        $refund->mode = 'live';

        $response = $refund->cancel();

        $this->assertNull($response);
    }

    public function testCancelRefundWithoutOauth()
    {
        $endpoint = $this->createMock(PaymentRefundEndpoint::class);
        $endpoint->expects($this->once())
            ->method('cancelForId')
            ->with(
                $this->equalTo('tr_abc'),  // paymentId
                $this->equalTo('re_123'),   // refundId
                $this->equalTo([])
            );

        $apiClient = $this->createMock(MollieApiClient::class);
        $apiClient->paymentRefunds = $endpoint;
        $apiClient->expects($this->once())
            ->method('usesOAuth')
            ->willReturn(false);

        $refund = new Refund($apiClient);
        $refund->id = 're_123';
        $refund->paymentId = 'tr_abc';
        $refund->mode = 'test';

        $response = $refund->cancel();

        $this->assertNull($response);
    }


    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestRefundStatuses
     */
    public function testRefundStatuses($status, $function, $expected_boolean)
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));
        $refund->status = $status;

        $this->assertEquals($expected_boolean, $refund->{$function}());
    }

    /**
     * @param string $status
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestRefundCanBeCanceled
     */
    public function testRefundCanBeCanceled($status, $expected_boolean)
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));
        $refund->status = $status;

        $this->assertEquals($expected_boolean, $refund->canBeCanceled());
    }

    public function dpTestRefundStatuses()
    {
        return [
            [RefundStatus::STATUS_PENDING, "isPending", true],
            [RefundStatus::STATUS_PENDING, "isProcessing", false],
            [RefundStatus::STATUS_PENDING, "isQueued", false],
            [RefundStatus::STATUS_PENDING, "isTransferred", false],
            [RefundStatus::STATUS_PENDING, "isFailed", false],

            [RefundStatus::STATUS_PROCESSING, "isPending", false],
            [RefundStatus::STATUS_PROCESSING, "isProcessing", true],
            [RefundStatus::STATUS_PROCESSING, "isQueued", false],
            [RefundStatus::STATUS_PROCESSING, "isTransferred", false],
            [RefundStatus::STATUS_PROCESSING, "isFailed", false],

            [RefundStatus::STATUS_QUEUED, "isPending", false],
            [RefundStatus::STATUS_QUEUED, "isProcessing", false],
            [RefundStatus::STATUS_QUEUED, "isQueued", true],
            [RefundStatus::STATUS_QUEUED, "isTransferred", false],
            [RefundStatus::STATUS_QUEUED, "isFailed", false],

            [RefundStatus::STATUS_REFUNDED, "isPending", false],
            [RefundStatus::STATUS_REFUNDED, "isProcessing", false],
            [RefundStatus::STATUS_REFUNDED, "isQueued", false],
            [RefundStatus::STATUS_REFUNDED, "isTransferred", true],
            [RefundStatus::STATUS_REFUNDED, "isFailed", false],

            [RefundStatus::STATUS_FAILED, "isPending", false],
            [RefundStatus::STATUS_FAILED, "isProcessing", false],
            [RefundStatus::STATUS_FAILED, "isQueued", false],
            [RefundStatus::STATUS_FAILED, "isTransferred", false],
            [RefundStatus::STATUS_FAILED, "isFailed", true],
        ];
    }

    public function dpTestRefundCanBeCanceled()
    {
        return [
            [RefundStatus::STATUS_PENDING, true],
            [RefundStatus::STATUS_PROCESSING, false],
            [RefundStatus::STATUS_QUEUED, true],
            [RefundStatus::STATUS_REFUNDED, false],
            [RefundStatus::STATUS_FAILED, false],
            [RefundStatus::STATUS_CANCELED, false],
        ];
    }
}
