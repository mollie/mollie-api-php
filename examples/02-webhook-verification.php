<?php
/*
 * Example 2 - How to verify Mollie API Payments in a webhook.
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
	 * Check if this is a test request by Mollie
	 */
	if (!empty($_GET['testByMollie']))
	{
		die('OK');
	}

	/*
	 * Retrieve the payment's current state.
	 */
	$payment  = $mollie->payments->get($_POST["id"]);
	$order_id = $payment->metadata->order_id;

	/*
	 * Update the order in the database.
	 */
	database_write($order_id, $payment->status);

	if ($payment->isPaid() == TRUE)
	{
		/*
		 * At this point you'd probably want to start the process of delivering the product to the customer.
		 */
	}
	elseif ($payment->isOpen() == FALSE)
	{
		/*
		 * The payment isn't paid and isn't open anymore. We can assume it was aborted.
		 */
	}
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}


/*
 * NOTE: This example uses a text file as a database. Please use a real database like MySQL in production code.
 */
function database_write ($order_id, $status)
{
	$order_id = intval($order_id);
	$database = dirname(__FILE__) . "/orders/order-{$order_id}.txt";

	file_put_contents($database, $status);
}
