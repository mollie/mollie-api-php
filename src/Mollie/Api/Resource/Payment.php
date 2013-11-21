<?php

/**
 * This object represents a single payment.
 */
class Mollie_Api_Resource_Payment
{
	const METHOD_IDEAL       = "ideal";
	const METHOD_PAYSAFECARD = "paysafecard";
	const METHOD_CREDITCARD  = "creditcard";
	const METHOD_MISTERCASH  = "mistercash";

	/**
	 * Id of the payment (at the Mollie side). You need not to set this yourself, the API will create an ID for you.
	 *
	 * @var $id
	 */
	public $id;

	/**
	 * The amount of the payment.
	 *
	 * @var float|int The amount in EUROs.
	 */
	public $amount;

	/**
	 * If no payment method is set, the customer will be able to choose his / her preferred
	 * payment method him-/herself.
	 *
	 * @var string One of the METHOD_* constants.
	 */
	public $method;

	/**
	 * Set the description. This is visible to the customer e.g. during payment and on the bank or credit card
	 * statement.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Date and time the payment was created in ISO-8601 format.
	 *
	 * @var string
	 */
	public $createdDatetime;

	const STATUS_OPEN = "open";
	const STATUS_PAID = "paid";

	/**
	 * The status that was retrieved from the API in this object. You do not need to call this method yourself, this
	 * used by the Mollie_Api_Resource_Payment class.
	 *
	 * @var string
	 */
	public $status = self::STATUS_OPEN;

	/**
	 * Is this payment paid for?
	 *
	 * @return bool
	 */
	public function isPaid ()
	{
		return $this->status == self::STATUS_PAID;
	}

	/**
	 * @var stdClass
	 */
	public $links;

	/**
	 * Get the URL where the customer should be redirected to after creating the payment through the API.
	 *
	 * @return null|string
	 */
	public function getPaymentUrl()
	{
		if (!empty($this->links->paymentUrl))
		{
			return $this->links->paymentUrl;
		}
		return NULL;
	}
}