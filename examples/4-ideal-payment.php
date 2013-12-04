<?php
/**
 * Example 4 - How to prepare an iDEAL payment with the Mollie API.
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
	 * First, let the customer pick the bank in a simple HTML form. This step is actually optional.
	 */
	if ($_SERVER["REQUEST_METHOD"] != "POST")
	{
		$issuers = $mollie->issuers->all();

		echo '<form method="post">Select your bank: <select name="issuer">';

		foreach ($issuers as $issuer)
		{
			if ($issuer->method == Mollie_API_Object_Method::IDEAL)
			{
				echo '<option value=' . htmlspecialchars($issuer->id) . '>' . htmlspecialchars($issuer->name) . '</option>';
			}
		}

		echo '<option value="">or select later</option>';
		echo '</select><button>OK</button></form>';
		exit;
	}

	/*
	 * Generate a unique order id for this example. It is important to include this unique attribute
	 * in the redirectUrl (below) so a proper return page can be shown to the customer.
	 */
	$order_id = time();

	/*
	 * Payment parameters:
	 *   amount        Amount in EUROs. This example creates a € 27.50 payment.
	 *   method        Payment method "ideal".
	 *   description   Description of the payment.
	 *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
	 *   metadata      Custom metadata that is stored with the payment.
	 *   issuer        The customer's bank. If empty the customer can select it later.
	 */
	$payment = $mollie->payments->create(array(
		"amount"       => 27.50,
		"method"       => Mollie_API_Object_Method::IDEAL,
		"description"  => "My first iDEAL payment",
		"redirectUrl"  => dirname($_SERVER['SCRIPT_URI']) . "/3-return-page.php?order_id={$order_id}",
		"metadata"     => array(
			"order_id" => $order_id,
		),
		"issuer"       => !empty($_POST["issuer"]) ? $_POST["issuer"] : NULL
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

