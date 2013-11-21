<?php

/**
 * This object represents a single issuer.
 *
 * Issuers can be used for iDEAL payments to completely integrate iDEAL payments in your web site.
 */
class Mollie_Api_Resource_Issuer
{
	/**
	 * Id of the issuer.
	 *
	 * @var $id
	 */
	public $id;

	/**
	 * Name of the issuer.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The payment method this issues belongs to.
	 *
	 * @var string One of the METHOD_* constants.
	 */
	public $method;
}