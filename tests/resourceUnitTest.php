<?php

use Mollie\Api\Endpoints\PaymentEndpoint;
use Mollie\Api\Endpoints\PaymentRefundEndpoint;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

class Mollie_API_Resource_BaseUnitTest extends PHPUnit_Framework_TestCase
{
    public function testGetResourcePathTopLevelWorks()
    {
        $resource = new PaymentEndpoint(new MollieApiClient());

        $this->assertSame("payments", $resource->getResourcePath());
    }

    /**
     * @expectedException \Mollie\Api\Exceptions\ApiException
     * @expectedExceptionMessage Subresource 'payments_refunds' used without parent 'payments' ID.
     */
    public function testGetResourcePathSubresourceFails()
    {
        $resource = new PaymentRefundEndpoint(new MollieApiClient());
        $resource->getResourcePath();
    }

    public function testGetResourcePathSubresourceWorks()
    {
        $resource = new PaymentRefundEndpoint(new MollieApiClient());

        $this->assertSame("payments/tr_1237191/refunds", $resource->withParentId("tr_1237191")->getResourcePath());
    }

    public function testSetResourcePathWorks()
    {
        $resource = new PaymentRefundEndpoint(new MollieApiClient());
        $resource->setResourcePath("requests_responses");

        $this->assertSame("requests/req_8192398/responses", $resource->withParentId("req_8192398")->getResourcePath());
    }

    public function testWithWorks()
    {
        $resource = new PaymentRefundEndpoint(new MollieApiClient());
        $payment = new Payment();
        $payment->id = "tr_1237191";

        $this->assertSame("payments/tr_1237191/refunds", $resource->with($payment)->getResourcePath());
    }
}
