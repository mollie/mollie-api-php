<?php

require_once dirname(__FILE__) . "/../Mollie/Autoloader.php";

/*
 * After your customer has completed the payment, we will call your webhook to inform you of the change in state of the
 * payment. You can configure the webhook in the Mollie Beheer system (https://www.mollie.nl/beheer/). Your webhook
 * should check the state of the payment and update your order accordingly.
 */

/*
 * First, initialize the API with your API key. You can find the API key on:
 * https://www.mollie.nl/beheer/account/profielen/
 */
$api = new Mollie_Api_Payments("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

/**
 *
 */
$payment = $api->get("tr_go2amqdDKBx");

if ($payment->isPaid())
{
	/*
	 * Update your order to paid.
	 */
	echo "Your payment of € {$payment->amount} has been paid.";
}
else
{
	/*
	 * Update your order to a state that matches the payment state.
	 */
	echo "This payment has not yet been completed, it's current state is {$payment->status}.";
}

