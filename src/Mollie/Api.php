<?php

class Mollie_Api
{
	/**
	 * Version of our client
	 */
	const CLIENT_VERSION = "v0.1";

	/**
	 * Version of the remote API.
	 */
	const API_VERSION = "v1";

	/**
	 * Endpoint of the remote API.
	 */
	const API_ENDPOINT = "http://api.mollie.local/";

	/**
	 * @var Mollie_Api_Payments
	 */
	public $payments;

	/**
	 * @var Mollie_Api_Issuers
	 */
	public $issuers;

	/**
	 * @var string
	 */
	protected $api_key;

	/**
	 * @param string $api_key The Mollie API key, starting with "test_" or "live_"
	 */
	public function __construct($api_key)
	{
		$this->setApiKey($api_key);

		$this->payments = new Mollie_Api_Payments($this);
		$this->issuers  = new Mollie_Api_Issuers($this);
	}

	/**
	 * Set the API key. Throws an exception if the API is not understood.
	 *
	 * @param $api_key
	 * @throws Mollie_Api_Exception
	 */
	protected function setApiKey($api_key)
	{
		if (!preg_match("!^(?:live|test)_\\w+\$!", $api_key))
		{
			throw new Mollie_Api_Exception("Invalid api key: \"{$api_key}\". An API key must start with \"test_\" or \"live_\".");
		}

		$this->api_key = $api_key;
	}

	const HTTP_GET    = "GET";
	const HTTP_POST   = "POST";
	const HTTP_DELETE = "DELETE";

	/**
	 * Perform an http call. This method is used by the resource specific classes. Please use the $payments property to
	 * perform operations on payments.
	 *
	 * @see $payments
	 * @see $isuers
	 *
	 * @param $http_method
	 * @param $api_method
	 * @param $http_body
	 *
	 * @return string
	 * @throws Mollie_Api_Exception
	 *
	 * @codeCoverageIgnore
	 */
	public function performHttpCall($http_method, $api_method, $http_body = NULL)
	{
		$url = self::API_ENDPOINT . self::API_VERSION . "/" . $api_method;

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		$request_headers = array(
			"Accept: application/json",
			"Authorization: Bearer {$this->api_key}",
			"User-Agent: Mollie PHP Universal API Client " . self::CLIENT_VERSION,
		);

		if ($http_body !== NULL)
		{
			$request_headers[] = "Content-Type: application/json";

			if ($http_method == self::HTTP_POST)
			{
				curl_setopt($ch, CURLOPT_POST, 1);
			}
			else
			{
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $http_body);
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

		$body = curl_exec($ch);

		if (curl_errno($ch) == CURLE_SSL_CACERT || curl_errno($ch) == CURLE_SSL_PEER_CERTIFICATE || curl_errno($ch) == 77 /* CURLE_SSL_CACERT_BADFILE (constant not defined in PHP though) */)
		{
			/*
			 * On some servers, the list of installed certificates is outdated or not present at all (the ca-bundle.crt
			 * is not installed). So we tell cURL which certificates we trust. Then we retry the requests.
			 */
			curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . DIRECTORY_SEPARATOR . "Api" . DIRECTORY_SEPARATOR . "certdata.txt");
			$body = curl_exec($ch);
		}

		if (strpos(curl_error($ch), "certificate subject name 'mollie.nl' does not match target host") !== FALSE)
		{
			/*
			 * On some servers, the wildcard SSL certificate is not processed correctly. This happens with OpenSSL 0.9.7
			 * from 2003.
			 */
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			$body = curl_exec($ch);
		}

		if (curl_errno($ch))
		{
			throw new Mollie_Api_Exception("Unable to communicate with Mollie (".curl_errno($ch)."): " . curl_error($ch));
		}

		return $body;
	}
}