<?php
/**
 * This class allows you to create payments and get their status et cetera.
 */
class Mollie_Api_Payments extends Mollie_Api_Base
{
	const RESOURCE = "payments";

	/**
	 * @return Mollie_Api_Resource_Payment
	 */
	protected function getResourceObject ()
	{
		return new Mollie_Api_Resource_Payment();
	}

	/**
	 * Create a payment with the remote API.
	 *
	 * @param array $data An array containing details on the payment. The following fields are supported:
	 *  - "amount" float The amount of the payment in EURO's.
	 *  - "description" string The description. Will be shown to the customer and be put on bank / card statements.
	 *  - "redirectUrl" string The URL where the customer will be redirected after the payment.
	 *  - "method" string Optional. The payment method you want the customer to use. If you leave this element out, the
	 *                    customer will be free to pick his / her favorite payment method.
	 *
	 * @throws Mollie_Api_Exception
	 * @return Mollie_Api_Resource_Payment
	 */
	public function create(array $data = array())
	{
		return parent::create($data);
	}

	/**
	 * Retrieve information on a single payment from Mollie.
	 *
	 * Will throw a Mollie_Api_Exception if the payment cannot be found.
	 *
	 * @param string $payment_id
	 *
	 * @throws Mollie_Api_Exception
	 * @return Mollie_Api_Resource_Payment
	 */
	public function get($payment_id)
	{
		return parent::get($payment_id);
	}

	/**
	 * Get all payments from Mollie.
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @throws Mollie_Api_Exception
	 * @return Mollie_Api_Resource_Payment[]|Mollie_Api_Resource_List
	 */
	public function all ($offset = 0, $limit = self::DEFAULT_LIMIT)
	{
		return parent::all($offset, $limit);
	}
}