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

		$this->compatibilityChecker = $this->getMockBuilder("Mollie_API_CompatibilityChecker")
			->setMethods(array("checkCompatibility"))
			->getMock();

		$this->api = $this->getMockBuilder("Mollie_API_Client")
			->setMethods(array("performHttpCall", "getCompatibilityChecker", "getLastHttpResponseStatusCode"))
			->disableOriginalConstructor()
			->getMock();

		$this->api->expects($this->any())
			->method("getCompatibilityChecker")
			->will($this->returnValue($this->compatibilityChecker));

		// Call constructor after set expectations
		$this->api->__construct();
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage Invalid API key: 'test_xxx'. An API key must start with 'test_' or 'live_'.
	 */
	public function testSettingInvalidApiKeyFails ()
	{
		$api = new Mollie_API_Client;

		$api->setApiKey("test_xxx");
	}

	public function testSettingValidApiKeyFailsNot ()
	{
		$api = new Mollie_API_Client;

		$api->setApiKey("test_QnRGwP5fwWWMNQTCLAH4xDt3rw8dAc"); // Should not throw
		$api->setApiKey("test_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"); // Should not throw
		$this->assertTrue(TRUE);
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage You have not set an API key or OAuth access token. Please use setApiKey() to set the API key.
	 */
	public function testNotSettingApiKeyGivesException()
	{
		$this->api = $this->getMockBuilder("Mollie_API_Client")
			->setMethods(array("getCompatibilityChecker"))
			->disableOriginalConstructor()
			->getMock();

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
			"amount"      => 100.00,
			"description" => "Order #1337 24 Roundhousekicks",
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
			"amount"      => 100.00,
			"description" => "Order #1337 24 Roundhousekicks",
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
			"amount"      => 100.00,
			"description" => "Order #1337 24 Roundhousekicks \x80 15,-",
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
			"amount"      => 100.00,
			"description" => "Order #1337 24 Roundhousekicks",
			"redirectUrl" => "http://www.chucknorris.rhk/return.php",
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
			->with(Mollie_API_Client::HTTP_POST, "payments/tr_OCrlrHqKsr/refunds", NULL)
			->will($this->returnValue('{"id":"re_O3UbDhODzG","payment":{"id":"tr_OCrlrHqKsr","mode":"live","createdDatetime":"2014-09-15T09:24:39.0Z","status":"refunded","expiryPeriod":"PT15M","paidDatetime":"2014-09-15T09:28:29.0Z","amount":"60.33","amountRefunded":"60.33","amountRemaining":null,"description":"15 Round House Kicks To The Face","method":"ideal","metadata":null,"details":{"consumerName":"Hr E G H K\u00fcppers en/of MW M.J. K\u00fcppers-Veeneman","consumerAccount":"NL53INGB0654422370","consumerBic":"INGBNL2A"},"links":{"redirectUrl":"http://www.example.org/return.php"}},"amount":"60.33","refundedDatetime":"2014-09-15T09:24:39.0Z"}'));

		$payment = new Mollie_API_Object_Payment();
		$payment->id = "tr_OCrlrHqKsr";

		/** @var Mollie_API_Object_Payment $payment */
		$refund = $this->api->payments->refund($payment);

		$this->assertEquals("re_O3UbDhODzG", $refund->id);
		$this->assertEquals(60.33, $refund->amount);
		$this->assertEquals("2014-09-15T09:24:39.0Z", $refund->refundedDatetime);

		$this->assertEquals("tr_OCrlrHqKsr", $payment->id);
		$this->assertEquals("15 Round House Kicks To The Face", $payment->description);
		$this->assertEquals(Mollie_API_Object_Method::IDEAL, $payment->method);
		$this->assertEquals("2014-09-15T09:24:39.0Z", $payment->createdDatetime);
		$this->assertEquals(Mollie_API_Object_Payment::STATUS_REFUNDED, $payment->status);

		$this->assertEquals(60.33, $payment->getAmountRefunded());
		$this->assertEquals(0, $payment->getAmountRemaining());

		$this->assertFalse($payment->canBeRefunded());
		$this->assertFalse($payment->canBePartiallyRefunded());

		$this->assertFalse($payment->isOpen());
		$this->assertFalse($payment->isExpired());
		$this->assertFalse($payment->isCancelled());
		$this->assertTrue($payment->isPaid());
		$this->assertTrue($payment->isRefunded());

		$this->assertNull($payment->metadata);
	}

	/**
	 * @group refunds
	 */
	public function testCreateRefundSupportsPartialRefunds ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_POST, "payments/tr_OCrlrHqKsr/refunds", '{"amount":60.33}')
			->will($this->returnValue('{"id":"re_O3UbDhODzG","payment":{"id":"tr_OCrlrHqKsr","mode":"live","createdDatetime":"2014-09-15T09:24:39.0Z","status":"refunded","expiryPeriod":"PT15M","paidDatetime":"2014-09-15T09:28:29.0Z","amount":"86.55","amountRefunded":"60.33","amountRemaining":"26.12","description":"15 Round House Kicks To The Face","method":"ideal","metadata":null,"details":{"consumerName":"Hr E G H K\u00fcppers en/of MW M.J. K\u00fcppers-Veeneman","consumerAccount":"NL53INGB0654422370","consumerBic":"INGBNL2A"},"links":{"redirectUrl":"http://www.example.org/return.php"}},"amount":60.33,"refundedDatetime":"2014-09-15T09:24:39.0Z"}'));

		$payment = new Mollie_API_Object_Payment();
		$payment->id = "tr_OCrlrHqKsr";

		/** @var Mollie_API_Object_Payment $payment */
		$refund = $this->api->payments->refund($payment, 60.33);

		$this->assertEquals(60.33, $refund->amount);

		$this->assertEquals(60.33, $payment->getAmountRefunded());
		$this->assertEquals(26.12, $payment->getAmountRemaining());

		$this->assertTrue($payment->canBeRefunded());
		$this->assertTrue($payment->canBePartiallyRefunded());
		$this->assertTrue($payment->isRefunded());
	}

	/**
	 * @group refunds
	 */
	public function testCreateRefundSupportsDescriptions ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_POST, "payments/tr_OCrlrHqKsr/refunds", '{"amount":60.33,"description":"Foo bar"}')
			->will($this->returnValue('{"id":"re_O3UbDhODzG","payment":{"id":"tr_OCrlrHqKsr","mode":"live","createdDatetime":"2014-09-15T09:24:39.0Z","status":"refunded","expiryPeriod":"PT15M","paidDatetime":"2014-09-15T09:28:29.0Z","amount":"86.55","amountRefunded":"60.33","amountRemaining":"26.12","description":"15 Round House Kicks To The Face","method":"ideal","metadata":null,"details":{"consumerName":"Hr E G H K\u00fcppers en/of MW M.J. K\u00fcppers-Veeneman","consumerAccount":"NL53INGB0654422370","consumerBic":"INGBNL2A"},"links":{"redirectUrl":"http://www.example.org/return.php"}},"amount":60.33,"description":"Foo bar","refundedDatetime":"2014-09-15T09:24:39.0Z"}'));

		$payment = new Mollie_API_Object_Payment();
		$payment->id = "tr_OCrlrHqKsr";

		/** @var Mollie_API_Object_Payment $payment */
		$refund = $this->api->payments->refund($payment, array("amount" => 60.33, "description" => "Foo bar"));

		$this->assertEquals(60.33, $refund->amount);
		$this->assertEquals("Foo bar", $refund->description);
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessageRegExp /Invalid payment ID: '.*?'. A payment ID should start with 'tr_'./
	 *
	 * @dataProvider dpInvalidPaymentId
	 */
	public function testGetPaymentFailsWithInvalidPaymentId ($payment_id)
	{
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
			$this->assertInstanceOf("Mollie_API_Object_Method", $method);
		}
	}

	public function testDeleteSubscriptionWorksCorrectly ()
	{
		$this->api->expects($this->once())
				  ->method("performHttpCall")
				  ->with(Mollie_API_Client::HTTP_DELETE, "customers/cst_3EA3vd0b0E/subscriptions/sub_d0b0E3EA3v")
				  ->will($this->returnValue('{"id":"sub_d0b0E3EA3v", "customerId":"cst_EA3vd0b0E3", "mode":"live", "createdDatetime":"2013-11-21T09:57:08.0Z", "status":"cancelled", "amount":100, "description":"Subscription #1225", "method":null, "times":null, "interval":"months", "cancelledDatetime":"2016-07-25T09:57:08.0Z"}'));

		/** @var Mollie_API_Object_Customer_Subscription $deleted_subscription */
		$deleted_subscription = $this->api->customers_subscriptions->withParentId("cst_3EA3vd0b0E")->cancel("sub_d0b0E3EA3v");

		$this->assertEquals("sub_d0b0E3EA3v", $deleted_subscription->id);

		$this->assertTrue($deleted_subscription->isCancelled());
	}

	public function testDeleteCustomerWorksCorrectly ()
	{
		$this->api->expects($this->once())
			->method('getLastHttpResponseStatusCode')
			->willReturn(Mollie_API_Client::HTTP_STATUS_NO_CONTENT);

		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_DELETE, "customers/cst_3EA3vd0b0E")
			->willReturn(NULL);

		$this->api->customers->delete("cst_3EA3vd0b0E");
	}

	public function testUndefinedResourceCallsResourceEndpoint ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "foobars/foobar_ID?f=B")
			->will($this->returnValue("{}"));

		$this->api->FooBars->get("foobar_ID", array("f" => "B"));
	}

	/**
	 * @expectedException Mollie_API_Exception
	 * @expectedExceptionMessage Subresource 'foos_bars' used without parent 'foos' ID.
	 */
	public function testUndefinedSubResourceRequiresParentId ()
	{
		$this->api->expects($this->never())
			->method("performHttpCall");

		$this->api->Foos_Bars->get("bar_ID", array("f" => "B"));
	}

	public function testUndefinedSubResourceCallsSubresourceEndpointWithParentId ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "foos/foo_PARENT/bars/bar_CHILD?f=B")
			->will($this->returnValue("{}"));

		$this->api->Foos_Bars->withParentId("foo_PARENT")->get("bar_CHILD", array("f" => "B"));
	}

	public function testUndefinedSubResourceCallsSubresourceEndpointWithParentObject ()
	{
		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "foos/foo_PARENT/bars/bar_CHILD?f=B")
			->will($this->returnValue("{}"));

		$parent     = new stdClass;
		$parent->id = "foo_PARENT";

		$this->api->Foos_Bars->with($parent)->get("bar_CHILD", array("f" => "B"));
	}

	public function testCustomerUpdateWorksCorrectly ()
	{
		$customer_id = "cst_8wmqcHMN4U";

		$expected_customer_api_call_data = array(
			"name" => "",
			"email" => "",
			"locale" => "",
			"metadata" => array(
				"my_id" => "1234567"
			)
		);

		$return_value = array_merge($expected_customer_api_call_data, array(
			"id" => $customer_id
		));

		$customer = new Mollie_API_Object_Customer();
		$customer->id = $customer_id;
		$customer->name = $expected_customer_api_call_data['name'];
		$customer->email = $expected_customer_api_call_data['email'];
		$customer->locale = $expected_customer_api_call_data['locale'];
		$customer->metadata = $expected_customer_api_call_data['metadata'];

		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_POST, "customers/cst_8wmqcHMN4U", json_encode($expected_customer_api_call_data))
			->will($this->returnValue(json_encode($return_value)));

		/** @var Mollie_API_Object_Customer $updated_customer */
		$updated_customer = $this->api->customers->update($customer);

		self::assertEquals($updated_customer->id, $customer_id);
		self::assertEquals($updated_customer->name, $expected_customer_api_call_data['name']);
		self::assertEquals($updated_customer->email, $expected_customer_api_call_data['email']);
		self::assertEquals($updated_customer->locale, $expected_customer_api_call_data['locale']);
		self::assertEquals($updated_customer->metadata->my_id, $expected_customer_api_call_data['metadata']['my_id']);
	}

	public function testProfileApiKeyGetWorksCorrectly ()
	{
		$profile_id = "pfl_v9hTwCvYqw";
		$api_key_mode = "live";

		$return_value = array(
			"id" => $api_key_mode,
			"key" => "live_eSf9fQRwpsdfPY8y3tUFFmqjADRKyA",
			"createdDatetime" => "2016-09-19T12:31:09.0Z"
		);

		$this->api->expects($this->once())
			->method("performHttpCall")
			->with(Mollie_API_Client::HTTP_GET, "profiles/$profile_id/apikeys/$api_key_mode")
			->willReturn(json_encode($return_value));

		$api_key = $this->api->profiles_apikeys->withParentId($profile_id)->get($api_key_mode);

		self::assertEquals($api_key_mode, $api_key->id);
		self::assertEquals($return_value['key'], $api_key->key);
		self::assertEquals($return_value['createdDatetime'], $api_key->createdDatetime);

		self::assertTrue($api_key->isLiveKey());
		self::assertFalse($api_key->isTestKey());
	}

	public function testProfileUpdateWorksCorrectly ()
    {
        $profile_id = "pfl_v9hTwCvYqw";

        $expected_profile_api_call_data = array(
            "name" => "Mollie",
            "website" => "www.mollie.com",
            "email" => "info@mollie.com",
            "phone" => "06123827111",
            "categoryCode" => 5399,
            "mode" => "live"
        );

        $return_value = array_merge($expected_profile_api_call_data, array(
            "id" => $profile_id,
        ));

        $profile = new Mollie_API_Object_Profile();
        $profile->id = $profile_id;
        $profile->name = $expected_profile_api_call_data['name'];
        $profile->website = $expected_profile_api_call_data['website'];
        $profile->email = $expected_profile_api_call_data['email'];
        $profile->phone = $expected_profile_api_call_data['phone'];
        $profile->categoryCode = $expected_profile_api_call_data['categoryCode'];
        $profile->mode = $expected_profile_api_call_data['mode'];

        $this->api->expects($this->once())
            ->method("performHttpCall")
            ->with(Mollie_API_Client::HTTP_POST, "profiles/pfl_v9hTwCvYqw", json_encode($expected_profile_api_call_data))
            ->will($this->returnValue(json_encode($return_value)));

        /** @var Mollie_API_Object_Profile $updated_profile */
        $updated_profile = $this->api->profiles->update($profile);

        self::assertEquals($updated_profile->id, $profile_id);
        self::assertEquals($updated_profile->name, $expected_profile_api_call_data['name']);
        self::assertEquals($updated_profile->website, $expected_profile_api_call_data['website']);
        self::assertEquals($updated_profile->email, $expected_profile_api_call_data['email']);
        self::assertEquals($updated_profile->phone, $expected_profile_api_call_data['phone']);
        self::assertEquals($updated_profile->categoryCode, $expected_profile_api_call_data['categoryCode']);
        self::assertEquals($updated_profile->mode, $expected_profile_api_call_data['mode']);
    }

    public function testProfileApiKeyResetWorks ()
    {
        $profile_id = "pfl_v9hTwCvYqw";
        $api_key_mode = "live";

        $return_value = array(
            "id" => $api_key_mode,
            "key" => "live_eSf9fQRwpsdfPY8y3tUFFmqjADRKyA",
            "createdDatetime" => "2016-09-19T12:31:09.0Z"
        );

        $this->api->expects($this->once())
            ->method("performHttpCall")
            ->with(Mollie_API_Client::HTTP_POST, "profiles/$profile_id/apikeys/$api_key_mode")
            ->willReturn(json_encode($return_value));

        /** @var Mollie_API_Object_Profile_APIKey $updated_api_key */
        $updated_api_key = $this->api->profiles_apikeys->withParentId($profile_id)->reset($api_key_mode);

        self::assertEquals($api_key_mode, $updated_api_key->id);
        self::assertEquals($return_value['key'], $updated_api_key->key);
        self::assertEquals($return_value['createdDatetime'], $updated_api_key->createdDatetime);

        self::assertTrue($updated_api_key->isLiveKey());
        self::assertFalse($updated_api_key->isTestKey());
    }
}
