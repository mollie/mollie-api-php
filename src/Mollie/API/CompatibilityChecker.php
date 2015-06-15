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
class Mollie_API_CompatibilityChecker
{
	/**
	 * @var string
	 */
	public static $MIN_PHP_VERSION = '5.2.0';

	/**
	 * Used cURL functions
	 *
	 * @var array
	 */
	public static $REQUIRED_CURL_FUNCTIONS = array(
		'curl_init',
		'curl_setopt',
		'curl_exec',
		'curl_error',
		'curl_errno',
		'curl_close',
		'curl_version',
	);

	/**
	 * @throws Mollie_API_Exception_IncompatiblePlatform
	 * @return void
	 */
	public function checkCompatibility ()
	{
		if (!$this->satisfiesPhpVersion())
		{
			throw new Mollie_API_Exception_IncompatiblePlatform(
				"The client requires PHP version >= " . self::$MIN_PHP_VERSION . ", you have " . PHP_VERSION . ".",
				Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_PHP_VERSION
			);
		}

		if (!$this->satisfiesJsonExtension())
		{
			throw new Mollie_API_Exception_IncompatiblePlatform(
				"PHP extension json is not enabled. Please make sure to enable 'json' in your PHP configuration.",
				Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_JSON_EXTENSION
			);
		}

		if (!$this->satisfiesCurlExtension())
		{
			throw new Mollie_API_Exception_IncompatiblePlatform(
				"PHP extension cURL is not enabled. Please make sure to enable 'curl' in your PHP configuration.",
				Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_CURL_EXTENSION
			);
		}

		if (!$this->satisfiesCurlFunctions())
		{
			throw new Mollie_API_Exception_IncompatiblePlatform(
				"This client requires the following cURL functions to be available: " . implode(', ', self::$REQUIRED_CURL_FUNCTIONS) . ". " .
				"Please check that none of these functions are disabled in your PHP configuration.",
				Mollie_API_Exception_IncompatiblePlatform::INCOMPATIBLE_CURL_FUNCTION
			);
		}
	}

	/**
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public function satisfiesPhpVersion ()
	{
		return (bool) version_compare(PHP_VERSION, self::$MIN_PHP_VERSION, ">=");
	}

	/**
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public function satisfiesJsonExtension ()
	{
		// Check by extension_loaded
		if (function_exists('extension_loaded') && extension_loaded('json'))
		{
			return TRUE;
		}
		elseif (function_exists('json_encode'))
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public function satisfiesCurlExtension ()
	{
		// Check by extension_loaded
		if (function_exists('extension_loaded') && extension_loaded('curl'))
		{
			return TRUE;
		}
		// Check by calling curl_version()
		elseif (function_exists('curl_version') && curl_version())
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public function satisfiesCurlFunctions ()
	{
		foreach (self::$REQUIRED_CURL_FUNCTIONS as $curl_function)
		{
			if (!function_exists($curl_function))
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}