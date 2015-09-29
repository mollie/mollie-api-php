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

	// Check if this payment can be refunded
	// You can also check if the payment can be partially refunded
	// by using $payment->canBePartiallyRefunded() and $payment->getAmountRemaining()
	if ($payment->canBeRefunded())
	{
		/*
		 * Refund € 15,00 of the payment.
		 *
		 * https://www.mollie.com/en/docs/refunds#refund-create
		 */
		$refund = $mollie->payments->refund($payment, 15.00);

		echo "€ 15,00 of payment {$payment_id} refunded.", PHP_EOL;
	}
	else
	{
		echo "Payment {$payment_id} can not be refunded.", PHP_EOL;
	}

	/*
	 * Retrieve all refunds on a payment.
	 */
	var_dump($mollie->payments_refunds->with($payment)->all());
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
