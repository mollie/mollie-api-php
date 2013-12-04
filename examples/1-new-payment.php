<?php
/*
 * Example 1 - How to prepare a new payment with the Mollie API.
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
	 * Generate a unique order id for this example. It is important to include this unique attribute
	 * in the redirectUrl (below) so a proper return page can be shown to the customer.
	 */
	$order_id = time();

	/*
	 * Payment parameters:
	 *   amount        Amount in EUROs. This example creates a € 10,- payment.
	 *   description   Description of the payment.
	 *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
	 *   metadata      Custom metadata that is stored with the payment.
	 */
	$payment = $mollie->payments->create(array(
		"amount"       => 10.00,
		"description"  => "My first API payment",
		"redirectUrl"  => dirname($_SERVER['SCRIPT_URI']) . "/3-return-page.php?order_id={$order_id}",
		"metadata"     => array(
			"order_id" => $order_id,
		),
	));

	/*
	 * In this example we store the order with its payment status in a database.
	 */
	database_write($order_id, $payment->status);

	/*
	 * Send the customer off to complete the payment.
	 */
	header("Location: " . $payment->getPaymentUrl());
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
