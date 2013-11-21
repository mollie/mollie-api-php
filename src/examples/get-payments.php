<?php
/*
 * If you wish to have some overview of all the payments you have with Mollie, you can programmatically create an
 * overview using our API.
 */
require_once dirname(__FILE__) . "/../Mollie/Autoloader.php";

/*
 * First, initialize the API with your API key. You can find the API key on:
 * https://www.mollie.nl/beheer/account/profielen/
 */
$api = new Mollie_Api("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

/*
 * You can retrieve the payments in batches.
 */
$payments = $api->payments->all();

foreach ($payments as $payment)
{
	echo "€ " . htmlspecialchars($payment->amount) . ", status: " . htmlspecialchars($payment->status) . "<br>";
}
