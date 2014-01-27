<?php
/*
 * Example 5 - How to retrieve your payments history.
 */
require_once dirname(__FILE__) . "/../src/Mollie/API/Autoloader.php";

try
{
	/*
	 * Initialize the Mollie API library with your API key.
	 *
	 * See: https://www.mollie.nl/beheer/account/profielen/
	 */
	$mollie = new Mollie_API_Client;
	$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

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
