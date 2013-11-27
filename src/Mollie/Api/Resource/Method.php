<?php

/**
 * This object represents a payment method.
 *
 * This list of enabled payment methods can be retrieved from the Mollie API dynamically.
 */
class Mollie_Api_Resource_Method
{
	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var stdClass
	 */
	public $amount;
}