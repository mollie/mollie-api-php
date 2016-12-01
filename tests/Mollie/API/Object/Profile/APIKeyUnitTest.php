<?php

class Mollie_API_Object_Profiles_APIKeyUnitTest extends PHPUnit_Framework_TestCase
{
	public function testLiveApiKey ()
	{
		$api_key = new Mollie_API_Object_Profile_APIKey();
		$api_key->id = "live";

		self::assertTrue($api_key->isLiveKey());
		self::assertFalse($api_key->isTestKey());
	}

	public function testTestApiKey ()
	{
		$api_key = new Mollie_API_Object_Profile_APIKey();
		$api_key->id = "test";

		self::assertTrue($api_key->isTestKey());
		self::assertFalse($api_key->isLiveKey());
	}
}
