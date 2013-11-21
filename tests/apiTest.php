<?php

class Mollie_ApiTest extends PHPUnit_Framework_TestCase
{
	const API_KEY = "test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM";

	/**
	 * @var Mollie_Api|PHPUnit_Framework_MockObject_MockObject
	 */
	protected $api;

	protected function setUp()
	{
		parent::setUp();
		$this->api = $this->getMock("Mollie_Api", array("performHttpCall"), array(self::API_KEY));
	}

	/**
	 * @expectedException Mollie_Api_Exception
	 * @expectedExceptionMessage Invalid api key: "invalid". An API key must start with "test_" or "live_".
	 */
	public function testSettingInvalidApiKeyFails ()
	{
		$this->api = new Mollie_Api("invalid");
	}

	/**
	 * @expectedException Mollie_Api_Exception
	 * @expectedExceptionMessage Unable to decode Mollie response: ""
	 */
	public function testCreatePaymentFailsEmptyHttpBody ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_Api::HTTP_POST, Mollie_Api_Payments::RESOURCE, '{"amount":100,"description":"Order #1337 24 Roundhousekicks","redirect_uri":"http:\/\/www.chucknorris.rhk\/return.php"}')
			->will($this->returnValue(""));

		$this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks",
			"redirect_uri" => "http://www.chucknorris.rhk/return.php",
		));
	}

	/**
	 * @expectedException Mollie_Api_Exception
	 * @expectedExceptionMessage Error executing API call (request): Unauthorized request.
	 */
	public function testCreatePaymentFailsError ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_Api::HTTP_POST, Mollie_Api_Payments::RESOURCE, '{"amount":100,"description":"Order #1337 24 Roundhousekicks","redirect_uri":"http:\/\/www.chucknorris.rhk\/return.php"}')
			->will($this->returnValue('{ "error":{ "type":"request", "message":"Unauthorized request", "links":{ "documentation":"https://www.mollie.nl/api/docs/" } } }'));

		$this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks",
			"redirect_uri" => "http://www.chucknorris.rhk/return.php",
		));
	}

	public function testCreatePaymentWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_Api::HTTP_POST, Mollie_Api_Payments::RESOURCE, '{"amount":100,"description":"Order #1337 24 Roundhousekicks","redirect_uri":"http:\/\/www.chucknorris.rhk\/return.php"}')
			->will($this->returnValue('{ "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "state":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" } }'));

		/** @var Mollie_Api_Resource_Payment $payment */
		$payment = $this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks",
			"redirect_uri" => "http://www.chucknorris.rhk/return.php",
		));

		$this->assertEquals("tr_d0b0E3EA3v", $payment->id);
		$this->assertEquals("Order #1225", $payment->description);
		$this->assertNull($payment->method);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_Api_Resource_Payment::STATUS_OPEN, $payment->status);
		$this->assertFalse($payment->isPaid());
		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
	}

	public function testGetPaymentWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_Api::HTTP_GET, Mollie_Api_Payments::RESOURCE . "/tr_d0b0E3EA3v")
			->will($this->returnValue('{ "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "state":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" } }'));

		/** @var Mollie_Api_Resource_Payment $payment */
		$payment = $this->api->payments->get("tr_d0b0E3EA3v");

		$this->assertEquals("tr_d0b0E3EA3v", $payment->id);
		$this->assertEquals("Order #1225", $payment->description);
		$this->assertNull($payment->method);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_Api_Resource_Payment::STATUS_OPEN, $payment->status);
		$this->assertFalse($payment->isPaid());
		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
	}

	public function testGetPaymentsWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_Api::HTTP_GET, Mollie_Api_Payments::RESOURCE . "?offset=0&count=50")
			->will($this->returnValue('{
  "totalCount":1,
  "offset":0,
  "count":1,
  "data":[
    {
      "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "state":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" }
    }
  ],
  "links":{
    "first":null,
    "previous":null,
    "next":null,
    "last":null
  }
}'));

		$collection = $this->api->payments->all();
		$this->assertCount(1, $collection);

		/** @var Mollie_Api_Resource_Payment $payment */
		$payment = $collection[0];

		$this->assertEquals("tr_d0b0E3EA3v", $payment->id);
		$this->assertEquals("Order #1225", $payment->description);
		$this->assertNull($payment->method);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_Api_Resource_Payment::STATUS_OPEN, $payment->status);
		$this->assertFalse($payment->isPaid());
		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
	}
}