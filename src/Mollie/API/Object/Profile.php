<?php
/**
 * Copyright (c) 2015, Mollie B.V.
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
class Mollie_API_Object_Profile
{
	/**
	 * Id of the payment profile.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Either "live" or "test". Indicates this being a test or a live (verified) profile.
	 *
	 * @var string
	 */
	public $mode;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $website;

	/**
	 * @var string
	 */
	public $email;

	/**
	 * @var string
	 */
	public $phone;

	/**
	 * Merchant category code.
	 *
	 * @see https://www.mollie.com/en/docs/profiles#profiles-object
	 * @var int
	 */
	public $categoryCode;

	/**
	 * Profile status. "unverified", "verified" or "blocked".
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Review object with "status" property that's either "pending" or "rejected".
	 *
	 * @see https://www.mollie.com/en/docs/profiles#profiles-object
	 *
	 * @var object|null
	 */
	public $review;

	/**
	 * @var string
	 */
	public $createdDatetime;

	/**
	 * @var string
	 */
	public $updatedDatetime;

	/**
	 * If the App owner is also owner this profile, then links may contain a link
	 * to the live and test API keys of this profile.
	 *
	 * @var object
	 */
	public $links;
}
