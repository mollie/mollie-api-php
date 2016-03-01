<?php

class Mollie_API_Resource_BaseUnitTest extends PHPUnit_Framework_TestCase
{
	public function testGetResourcePathTopLevelWorks()
	{
		$resource = new Mollie_API_Resource_Payments(new Mollie_API_Client);

		$this->assertSame("payments", $resource->getResourcePath());
	}

	public function testGetResourcePathSubresourceFails()
	{
		$this->setExpectedException("Mollie_API_Exception", "Subresource 'payments_refunds' used without parent 'payments' ID.");

		$resource = new Mollie_API_Resource_Payments_Refunds(new Mollie_API_Client);
		$resource->getResourcePath();
	}

	public function testGetResourcePathSubresourceWorks()
	{
		$resource = new Mollie_API_Resource_Payments_Refunds(new Mollie_API_Client);

		$this->assertSame("payments/tr_1237191/refunds", $resource->withParentId("tr_1237191")->getResourcePath());
	}

	public function testSetResourcePathWorks()
	{
		$resource = new Mollie_API_Resource_Payments_Refunds(new Mollie_API_Client);
		$resource->setResourcePath("requests_responses");

		$this->assertSame("requests/req_8192398/responses", $resource->withParentId("req_8192398")->getResourcePath());
	}

	public function testWithWorks()
	{
		$resource    = new Mollie_API_Resource_Payments_Refunds(new Mollie_API_Client);
		$payment     = new Mollie_API_Object_Payment;
		$payment->id = "tr_1237191";

		$this->assertSame("payments/tr_1237191/refunds", $resource->with($payment)->getResourcePath());
	}
}
