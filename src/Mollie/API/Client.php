<?php
/**
 * Copyright (c) 2013, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @license     Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author      Mollie B.V. <info@mollie.com>
 * @copyright   Mollie B.V.
 * @link        https://www.mollie.com
 */
class Mollie_API_Client
{
	/**
	 * Version of our client.
	 */
	const CLIENT_VERSION = "1.2.7";

	/**
	 * Endpoint of the remote API.
	 */
	const API_ENDPOINT = "https://api.mollie.nl";

	/**
	 * Version of the remote API.
	 */
	const API_VERSION = "v1";

	const HTTP_GET    = "GET";
	const HTTP_POST   = "POST";
	const HTTP_DELETE = "DELETE";

	/**
	 * @var string
	 */
	protected $api_endpoint = self::API_ENDPOINT;

	/**
	 * RESTful Payments resource.
	 *
	 * @var Mollie_API_Resource_Payments
	 */
	public $payments;

	/**
	 * RESTful Payments Refunds resource.
	 *
	 * @var Mollie_API_Resource_Payments_Refunds
	 */
	public $payments_refunds;

	/**
	 * RESTful Issuers resource.
	 *
	 * @var Mollie_API_Resource_Issuers
	 */
	public $issuers;

	/**
	 * RESTful Methods resource.
	 *
	 * @var Mollie_API_Resource_Methods
	 */
	public $methods;

	/**
	 * @var string
	 */
	protected $api_key;

	/**
	 * @var array
	 */
	protected $version_strings = array();

	/**
	 * @var resource
	 */
	protected $ch;

	/**
	 * @throws Mollie_API_Exception_IncompatiblePlatform
	 */
	public function __construct ()
	{
		$this->getCompatibilityChecker()
			->checkCompatibility();

		$this->payments         = new Mollie_API_Resource_Payments($this);
		$this->payments_refunds = new Mollie_API_Resource_Payments_Refunds($this);
		$this->issuers          = new Mollie_API_Resource_Issuers($this);
		$this->methods          = new Mollie_API_Resource_Methods($this);

		$curl_version = curl_version();

		$this->addVersionString("Mollie/" . self::CLIENT_VERSION);
		$this->addVersionString("PHP/" . phpversion());
		$this->addVersionString("cURL/" . $curl_version["version"]);
		$this->addVersionString($curl_version["ssl_version"]);
	}

	/**
	 * @param string $url
	 */
	public function setApiEndpoint ($url)
	{
		$this->api_endpoint = rtrim(trim($url), '/');
	}

	/**
	 * @return string
	 */
	public function getApiEndpoint ()
	{
		return $this->api_endpoint;
	}

	/**
	 * @param string $api_key The Mollie API key, starting with 'test_' or 'live_'
	 * @throws Mollie_API_Exception
	 */
	public function setApiKey ($api_key)
	{
		$api_key = trim($api_key);

		if (!preg_match('/^(live|test)_\w+$/', $api_key))
		{
			throw new Mollie_API_Exception("Invalid API key: '{$api_key}'. An API key must start with 'test_' or 'live_'.");
		}

		$this->api_key = $api_key;
	}

	/**
	 * @param string $version_string
	 */
	public function addVersionString ($version_string)
	{
		$this->version_strings[] = str_replace(array(" ", "\t", "\n", "\r"), '-', $version_string);
	}

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
	 * @throws Mollie_API_Exception
	 *
	 * @codeCoverageIgnore
	 */
	public function performHttpCall ($http_method, $api_method, $http_body = NULL)
	{
		if (empty($this->api_key))
		{
			throw new Mollie_API_Exception("You have not set an API key. Please use setApiKey() to set the API key.");
		}

		if (empty($this->ch) || !function_exists("curl_reset"))
		{
			/*
			 * Initialize a cURL handle.
			 */
			$this->ch = curl_init();
		}
		else
		{
			/*
			 * Reset the earlier used cURL handle.
			 */
			curl_reset($this->ch);
		}

		$url = $this->api_endpoint . "/" . self::API_VERSION . "/" . $api_method;

		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->ch, CURLOPT_ENCODING, "");
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 10);

		$user_agent = join(' ', $this->version_strings);

		$request_headers = array(
			"Accept: application/json",
			"Authorization: Bearer {$this->api_key}",
			"User-Agent: {$user_agent}",
			"X-Mollie-Client-Info: " . php_uname(),
		);

		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $http_method);
		
		if ($http_body !== NULL)
		{
			$request_headers[] = "Content-Type: application/json";
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $http_body);
		}

		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $request_headers);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, TRUE);

		/*
		 * On some servers, the list of installed certificates is outdated or not present at all (the ca-bundle.crt
		 * is not installed). So we tell cURL which certificates we trust.
		 */
		curl_setopt($this->ch, CURLOPT_CAINFO, realpath(dirname(__FILE__) . "/cacert.pem"));

		$body = curl_exec($this->ch);

		if (curl_errno($this->ch) == CURLE_SSL_CACERT || curl_errno($this->ch) == CURLE_SSL_PEER_CERTIFICATE || curl_errno($this->ch) == 77 /* CURLE_SSL_CACERT_BADFILE (constant not defined in PHP though) */)
		{
			if (strpos(curl_error($this->ch), "error setting certificate verify locations") !== FALSE)
			{
				/*
				 * Error setting CA-file. Could be missing, or there is a bug in OpenSSL with too long paths.
				 * We give up. Don't do any peer validations.
				 *
				 * @internal #MOL017891004
				 */
				array_shift($request_headers);
				$request_headers[] = "X-Mollie-Debug: unable to use shipped root certificaties, no peer validation.";
				curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				$body = curl_exec($this->ch);
			}
		}

		if (strpos(curl_error($this->ch), "certificate subject name 'mollie.nl' does not match target host") !== FALSE)
		{
			/*
			 * On some servers, the wildcard SSL certificate is not processed correctly. This happens with OpenSSL 0.9.7
			 * from 2003.
			 */
			$request_headers[] = "X-Mollie-Debug: old OpenSSL found";
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, $request_headers);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
			$body = curl_exec($this->ch);
		}

		if (curl_errno($this->ch))
		{
			$message = "Unable to communicate with Mollie (".curl_errno($this->ch)."): " . curl_error($this->ch) . ".";

			curl_close($this->ch);
			$this->ch = NULL;

			throw new Mollie_API_Exception($message);
		}

		if (!function_exists("curl_reset"))
		{
			/*
			 * Keep it open if supported by PHP, else close the handle.
			 */
			curl_close($this->ch);
			$this->ch = NULL;
		}

		return $body;
	}

	/**
	 * Close any cURL handles, if we have them.
	 */
	public function __destruct ()
	{
		if (is_resource($this->ch))
		{
			curl_close($this->ch);
		}
	}

	/**
	 * @return Mollie_API_CompatibilityChecker
	 * @codeCoverageIgnore
	 */
	protected function getCompatibilityChecker ()
	{
		return new Mollie_API_CompatibilityChecker();
	}
}
