<?php

class Mollie_ApiUnitTest extends PHPUnit_Framework_TestCase
{
	const API_KEY = "test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM";

	/**
	 * @var Mollie_API_Client|PHPUnit_Framework_MockObject_MockObject
	 */
	protected $api;

	protected function setUp()
	{
		parent::setUp();
		$this->api = $this->getMock("Mollie_API_Client", array("performHttpCall"));
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage Invalid API key: 'invalid'. An API key must start with 'test_' or 'live_'.
	 */
	public function testSettingInvalidApiKeyFails ()
	{
		$this->api = new Mollie_API_Client;
		$this->api->setApiKey("invalid");
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage You have not set an API key. Please use setApiKey() to set the API key.
	 */
	public function testNotSettingApiKeyGivesException()
	{
		$this->api = new Mollie_API_Client;
		$this->api->payments->all();
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage Unable to decode Mollie response: ''.
	 */
	public function testCreatePaymentFailsEmptyHttpBody ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_POST, "payments", '{"amount":100,"description":"Order #1337 24 Roundhousekicks","redirectUrl":"http:\/\/www.chucknorris.rhk\/return.php"}')
			->will($this->returnValue(""));

		$this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks",
			"redirectUrl" => "http://www.chucknorris.rhk/return.php",
		));
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage Error executing API call (request): Unauthorized request.
	 */
	public function testCreatePaymentFailsError ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_POST, "payments", '{"amount":100,"description":"Order #1337 24 Roundhousekicks","redirectUrl":"http:\/\/www.chucknorris.rhk\/return.php"}')
			->will($this->returnValue('{ "error":{ "type":"request", "message":"Unauthorized request", "links":{ "documentation":"https://www.mollie.nl/api/docs/" } } }'));

		$this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks",
			"redirectUrl" => "http://www.chucknorris.rhk/return.php",
		));
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage Error encoding parameters into JSON: '5'.
	 * @requires PHP 5.3.0
	 */
	public function testCreatePaymentJsonFailsPhp53 ()
	{
		$this->api->expects($this->never())
			->method("performHttpCall");

		$this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks \x80 15,-",
			"redirectUrl" => "http://www.chucknorris.rhk/return.php",
		));
	}

	public function testCreatePaymentWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_POST, "payments", '{"amount":100,"description":"Order #1337 24 Roundhousekicks","redirectUrl":"http:\/\/www.chucknorris.rhk\/return.php"}')
			->will($this->returnValue('{ "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "status":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" } }'));

		/** @var Mollie_API_Object_Payment $payment */
		$payment = $this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks",
			"redirectUrl"  => "http://www.chucknorris.rhk/return.php",
		));

		$this->assertEquals("tr_d0b0E3EA3v", $payment->id);
		$this->assertEquals("Order #1225", $payment->description);
		$this->assertNull($payment->method);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_API_Object_Payment::STATUS_OPEN, $payment->status);
		$this->assertFalse($payment->isPaid());
		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
		$this->assertNull($payment->metadata);
	}

	/**
	 * @dataProvider dpInvalidPaymentId
	 */
	public function testGetPaymentFailsWithInvalidPaymentId ($payment_id)
	{
		$this->setExpectedException('Mollie_API_Exception', "Invalid payment ID: '{$payment_id}'. A payment ID should start with 'tr_'.");

		$this->api->payments->get($payment_id);
	}

	public function dpInvalidPaymentId ()
	{
		return array(
			array(NULL),
			array(''),
			array('d0b0E3EA3v')
		);
	}

	public function testGetPaymentWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "payments/tr_d0b0E3EA3v")
			->will($this->returnValue('{ "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "status":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" } }'));

		/** @var Mollie_API_Object_Payment $payment */
		$payment = $this->api->payments->get("tr_d0b0E3EA3v");

		$this->assertEquals("tr_d0b0E3EA3v", $payment->id);
		$this->assertEquals("Order #1225", $payment->description);
		$this->assertNull($payment->method);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_API_Object_Payment::STATUS_OPEN, $payment->status);
		$this->assertFalse($payment->isPaid());
		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
		$this->assertNull($payment->metadata);
	}

	public function testGetPaymentsWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "payments?offset=0&count=0")
			->will($this->returnValue('{
  "totalCount":1,
  "offset":0,
  "count":1,
  "data":[
    {
      "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "status":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" }
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

		/** @var Mollie_API_Object_Payment $payment */
		$payment = $collection[0];

		$this->assertEquals("tr_d0b0E3EA3v", $payment->id);
		$this->assertEquals("Order #1225", $payment->description);
		$this->assertNull($payment->method);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_API_Object_Payment::STATUS_OPEN, $payment->status);
		$this->assertFalse($payment->isPaid());
		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
		$this->assertNull($payment->metadata);
	}
}
