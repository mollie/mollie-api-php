<?php

require_once dirname(__FILE__) . "/../Mollie/Autoloader.php";

/*
 * First, initialize the API with your API key. You can find the API key on:
 * https://www.mollie.nl/beheer/account/profielen/. It must start with "test_" or "live_".
 */
$api = new Mollie_Api("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

/*
 * Create a € 100 payment with the description "Order #1225". No payment method is specified.
 *
 * The method argument is optional. If you leave it out, your customer will be allowed to pick his / her preferred payment
 * method. You can select which payment method you want to allow in your Mollie beheer.
 *
 * It is recommended not to force the customer to a specific payment method here, but instead to let the customer pick
 * their own payment method. That way you can easily enable new payment methods if Mollie adds these.
 */
$data = array(
	"amount"       => 100.00, // € 100
	"description"  => "Order #1225",
	"redirectUrl"  => "http://www.example.org/return.php",
	"metadata"     => array(
		"order_id" => "133",
		"customer" => array(
			"email" => "chuck@norris.rhk",
			"id" => 25,
		),
		"order" => array("amount" => 15, "currency" => "€"),
	),
);

$payment = $api->payments->create($data);

/*
 * Now you have to store the id of the payment with your customer's order. You will need this later on update the order
 * state if we inform you through a webhook of a new state of the payment.
 */

/*
 * Now that we have created the payment and stored its id with the order, redirect the customer to the payment URL. Here
 * the customer can perform the payment using his / her favorite payment method.
 */
header("Location: {$payment->getPaymentUrl()}");