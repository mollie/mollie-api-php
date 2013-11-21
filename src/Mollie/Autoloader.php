<?php
/**
 * Copyright (c) 2012, Mollie B.V.
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
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Copyright © 2012 Mollie B.V.
 * @link        https://www.mollie.nl
 * @category    Mollie
 * @version     1.0
 *
 * Autoloader for Mollie classes.
 * 
 * @codeCoverageIgnore
 */
final class Mollie_Autoloader
{
	/**
	 * @var string
	 */
	const MOLLIE_PREFIX = "Mollie_";

	/**
	 * Uses require_once to load the Mollie class.
	 * 
	 * @param string $class_name
	 */
	public static function load ($class_name)
	{
		$file_name = str_replace(self::MOLLIE_PREFIX, "", $class_name, $count);
		if (empty($count))
		{
			return;
		}

		$file_name = str_replace("_", DIRECTORY_SEPARATOR, $file_name);
		require_once __DIR__ . DIRECTORY_SEPARATOR . "{$file_name}.php";
	}

	/**
	 * Register this autoloader.
	 * 
	 * @return bool
	 */
	public static function register ()
	{
		return spl_autoload_register(array(__CLASS__, "load"));
	}

	/**
	 * Unregister this autoloader.
	 * 
	 * @return bool
	 */
	public static function unregister ()
	{
		return spl_autoload_unregister(array(__CLASS__, "load"));
	}
}
/*
 * Initialize the autoloader.
 */
Mollie_Autoloader::register();
