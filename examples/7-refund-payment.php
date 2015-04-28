<?php
/*
 * Example 7 - How to refund a payment programmatically
 */
try
{
	/*
	 * Initialize the Mollie API library with your API key.
	 *
	 * See: https://www.mollie.com/beheer/account/profielen/
	 */
	include "initialize.php";

	/*
	 * Retrieve the payment you want to refund from the API.
	 */
	$payment_id = "tr_q2cLW9pxMT";
	$payment = $mollie->payments->get($payment_id);

	/*
	 * Refund € 15,00 of the payment.
	 */
	$refund = $mollie->payments->refund($payment, 15.00);

	echo "The payment {$payment_id} is now refunded.", PHP_EOL;

	/*
	 * Retrieve all refunds on a payment.
	 */
	var_dump($mollie->payments_refunds->with($payment)->all());
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
