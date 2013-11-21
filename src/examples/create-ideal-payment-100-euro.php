<?php

require_once dirname(__FILE__) . "/../Mollie/Autoloader.php";

/*
 * First, initialize the API with your API key. You can find the API key on:
 * https://www.mollie.nl/beheer/account/profielen/
 */
$api = new Mollie_Api("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

if ($_SERVER["REQUEST_METHOD"] != "POST")
{
	echo '<form method="post">';

	echo 'Kies uw bank: <select name="issuer">';
	foreach ($api->issuers->all() as $issuer)
	{
		echo '<option value=' . htmlspecialchars($issuer->id) . '>' . htmlspecialchars($issuer->name) . '</option>';
	}
	echo '</select>';
	echo '<button>OK</button>';
	echo '</form>';
}
else
{
	/*
	 * Create a € 100.00 iDEAL payment with the description "Order #1225".
	 *
	 * The method argument is optional. If you leave it out, your customer will be allowed to pick his / her preferred payment
	 * method. You can select which payment method you want to allow in your Mollie beheer.
	 *
	 * The issues argument is also optional. If you want, you can leave it out and the customer will be presented with
	 * a page where he / she can pick his / her issueing bank.
	 *
	 * It is recommended not to force the customer to a specific payment method here, but instead to let the customer pick
	 * their own payment method. That way you can easily enable new payment methods if Mollie adds these.
	 */
	$data = array(
		"amount"       => 100.00, // € 100
		"description"  => "Order #1225",
		"redirect_uri" => "http://www.example.org/return.php",
		"method"       => Mollie_Api_Resource_Payment::METHOD_IDEAL,
		"issuer"       => !empty($_POST["issuer"]) ? $_POST["issuer"] : NULL,
	);

	$payment = $api->payments->create($data);

	/*
	 * Now that we have created the payment and stored its id with the order, redirect the customer to the payment URL. Here
	 * payment.
	 */
	header("Location: {$payment->getPaymentUrl()}");
}