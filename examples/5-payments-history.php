<?php
/*
 * Example 5 - How to retrieve your payments history.
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
	 * Get the all payments for this API key ordered by newest.
	 */
	$payments = $mollie->payments->all();

	foreach ($mollie->payments->all() as $payment)
	{
		echo "&euro; " . htmlspecialchars($payment->amount) . ", status: " . htmlspecialchars($payment->status) . "<br>";
	}
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
