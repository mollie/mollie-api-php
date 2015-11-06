<?php

class Mollie_ApiUnitTest extends PHPUnit_Framework_TestCase
{
	const API_KEY = "test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM";

	/**
	 * @var Mollie_API_Client|PHPUnit_Framework_MockObject_MockObject
	 */
	protected $api;

	/**
	 * @var Mollie_API_CompatibilityChecker|PHPUnit_Framework_MockObject_MockObject
	 */
	protected $compatibilityChecker;

	protected function setUp()
	{
		parent::setUp();

		$this->compatibilityChecker = $this->getMock("Mollie_API_CompatibilityChecker", array("checkCompatibility"));
		$this->api                  = $this->getMock("Mollie_API_Client", array("performHttpCall", "getCompatibilityChecker"), array(), '', FALSE);

		$this->api->expects($this->any())
			->method("getCompatibilityChecker")
			->will($this->returnValue($this->compatibilityChecker));

		// Call constructor after set expectations
		$this->api->__construct();
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage Invalid API key: 'invalid'. An API key must start with 'test_' or 'live_'.
	 */
	public function testSettingInvalidApiKeyFails ()
	{
		$api = $this->getMock("Mollie_API_Client", NULL, array(), '', FALSE);
		$api->setApiKey("invalid");
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage You have not set an API key. Please use setApiKey() to set the API key.
	 */
	public function testNotSettingApiKeyGivesException()
	{
		$this->api = $this->getMock("Mollie_API_Client", array('getCompatibilityChecker'), array(), '', FALSE);
		$this->api->expects($this->any())
			->method("getCompatibilityChecker")
			->will($this->returnValue($this->compatibilityChecker));
		
		$this->api->__construct();
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
			->with(Mollie_API_Client::HTTP_POST, "payments?profileId=pfl_wdy%21aA6Zy", '{"amount":100,"description":"Order #1337 24 Roundhousekicks","redirectUrl":"http:\/\/www.chucknorris.rhk\/return.php"}')
			->will($this->returnValue(""));

		$this->api->payments->create(array(
			"amount"       => 100.00,
			"description"  => "Order #1337 24 Roundhousekicks",
			"redirectUrl" => "http://www.chucknorris.rhk/return.php",
		), array(
			"profileId" => "pfl_wdy!aA6Zy",
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
		$this->assertTrue($payment->isOpen());
		$this->assertFalse($payment->isPaid());
		$this->assertFalse($payment->isExpired());
		$this->assertFalse($payment->isCancelled());

		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
		$this->assertNull($payment->metadata);
	}

	/**
	 * @group refunds
	 */
	public function testCreateRefundWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_POST, "payments/tr_OCrlrHqKsr/refunds", '{"amount":60.33}')
			->will($this->returnValue('{"id":"re_O3UbDhODzG","payment":{"id":"tr_OCrlrHqKsr","mode":"live","createdDatetime":"2014-09-15T09:24:39.0Z","status":"refunded","expiryPeriod":"PT15M","paidDatetime":"2014-09-15T09:28:29.0Z","amount":"100.00","amountRefunded":"60.33","description":"15 Round House Kicks To The Face","method":"ideal","metadata":null,"details":{"consumerName":"Hr E G H K\u00fcppers en/of MW M.J. K\u00fcppers-Veeneman","consumerAccount":"NL53INGB0654422370","consumerBic":"INGBNL2A"},"links":{"redirectUrl":"http://www.example.org/return.php"}},"amount":"60.33","refundedDatetime":"2014-09-15T09:24:39.0Z"}'));

		$payment = new Mollie_API_Object_Payment();
		$payment->id = "tr_OCrlrHqKsr";

		/** @var Mollie_API_Object_Payment $payment */
		$refund = $this->api->payments->refund($payment, 60.33);

		$this->assertEquals("re_O3UbDhODzG", $refund->id);
		$this->assertEquals(60.33, $refund->amount);
		$this->assertEquals("2014-09-15T09:24:39.0Z", $refund->refundedDatetime);

		$this->assertEquals("tr_OCrlrHqKsr", $payment->id);
		$this->assertEquals("15 Round House Kicks To The Face", $payment->description);
		$this->assertEquals(Mollie_API_Object_Method::IDEAL, $payment->method);
		$this->assertEquals("2014-09-15T09:24:39.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_API_Object_Payment::STATUS_REFUNDED, $payment->status);

		$this->assertFalse($payment->isOpen());
		$this->assertFalse($payment->isExpired());
		$this->assertFalse($payment->isCancelled());
		$this->assertTrue($payment->isPaid());
		$this->assertTrue($payment->isRefunded());

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
			->with(Mollie_API_Client::HTTP_GET, "payments/tr_d0b0E3EA3v?count=1&profileId=pfl_wdy%21aA6Zy")
			->will($this->returnValue('{ "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "status":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" } }'));

		/** @var Mollie_API_Object_Payment $payment */
		$payment = $this->api->payments->get("tr_d0b0E3EA3v", array(
			"count"     => "1",
			"profileId" => "pfl_wdy!aA6Zy",
		));

		$this->assertEquals("tr_d0b0E3EA3v", $payment->id);
		$this->assertEquals("Order #1225", $payment->description);
		$this->assertNull($payment->method);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_API_Object_Payment::STATUS_OPEN, $payment->status);

		$this->assertTrue($payment->isOpen());
		$this->assertFalse($payment->isPaid());
		$this->assertFalse($payment->isExpired());
		$this->assertFalse($payment->isCancelled());

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
      "id":"tr_d0b0E3EA3v", "mode":"test", "createdDatetime":"2013-11-21T09:57:08.0Z", "expiryPeriod": "P12DT11H30M45S", "status":"open", "amount":100, "description":"Order #1225", "method":null, "details":null, "links":{ "paymentUrl":"https://www.mollie.nl/payscreen/pay/d0b0E3EA3v" }
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
		$this->assertEquals("P12DT11H30M45S", $payment->expiryPeriod);
		$this->assertEquals("2013-11-21T09:57:08.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_API_Object_Payment::STATUS_OPEN, $payment->status);
		$this->assertTrue($payment->isOpen());
		$this->assertFalse($payment->isPaid());
		$this->assertFalse($payment->isExpired());
		$this->assertFalse($payment->isCancelled());
		$this->assertEquals("https://www.mollie.nl/payscreen/pay/d0b0E3EA3v", $payment->getPaymentUrl());
		$this->assertNull($payment->metadata);
	}

	public function testMethodsWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "methods?offset=0&count=0&locale=de")
			->will($this->returnValue('{"totalCount":4,"offset":0,"count":4,"data":[{"id":"sofort","description":"SOFORT \u00dcberweisung","amount":{"minimum":"0.31","maximum":"5000.00"},"image":{"normal":"https://www.mollie.com/images/payscreen/methods/sofort.png","bigger":"https://www.mollie.com/images/payscreen/methods/sofort@2x.png"}},{"id":"ideal","description":"iDEAL","amount":{"minimum":"0.55","maximum":"50000.00"},"image":{"normal":"https://www.mollie.com/images/payscreen/methods/ideal.png","bigger":"https://www.mollie.com/images/payscreen/methods/ideal@2x.png"}},{"id":"mistercash","description":"Bancontact/Mister Cash","amount":{"minimum":"0.31","maximum":"10000.00"},"image":{"normal":"https://www.mollie.com/images/payscreen/methods/mistercash.png","bigger":"https://www.mollie.com/images/payscreen/methods/mistercash@2x.png"}},{"id":"belfius","description":"Belfius Direct Net","amount":{"minimum":"0.31","maximum":"50000.00"},"image":{"normal":"https://www.mollie.com/images/payscreen/methods/belfius.png","bigger":"https://www.mollie.com/images/payscreen/methods/belfius@2x.png"}}]}'));

		$methods = $this->api->methods->all(0, 0, array("locale" => "de"));

		$this->assertCount(4, $methods);
		$this->assertInstanceOf("Mollie_API_Object_List", $methods);

		foreach ($methods as $method)
		{
			$this->assertInstanceof("Mollie_API_Object_Method", $method);
		}
	}

	public function testUndefinedResourceCallsResourceEndpoint ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "foobars/foobar_ID?f=B")
			->will($this->returnValue("{}"));

		$this->api->FooBars->get("foobar_ID", array("f" => "B"));
	}

	public function testUndefinedSubresourceRequiresParentId ()
	{
		$this->api->expects($this->never())
			->method("performHttpCall");

		$this->setExpectedException("Mollie_API_Exception", "Subresource 'foos_bars' used without parent 'foos' ID.");

		$this->api->Foos_Bars->get("bar_ID", array("f" => "B"));
	}

	public function testUndefinedSubesourceCallsSubresourceEndpointWithParentId ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "foos/foo_PARENT/bars/bar_CHILD?f=B")
			->will($this->returnValue("{}"));

		$this->api->Foos_Bars->withParentId("foo_PARENT")->get("bar_CHILD", array("f" => "B"));
	}

	public function testUndefinedSubesourceCallsSubresourceEndpointWithParentObject ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "foos/foo_PARENT/bars/bar_CHILD?f=B")
			->will($this->returnValue("{}"));

		$parent     = new stdClass;
		$parent->id = "foo_PARENT";

		$this->api->Foos_Bars->with($parent)->get("bar_CHILD", array("f" => "B"));
	}
}
