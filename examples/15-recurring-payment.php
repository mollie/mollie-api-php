<?php
/*
 * Example 15 - How to create an on-demand recurring payment.
 */

try
{
	/*
	 * Initialize the Mollie API library with your API key or OAuth access token.
	 */
	include "initialize.php";

	/*
	 * Retrieve the last created customer for this example.
	 * If no customers are created yet, run example 11.
	 */
	$customer = $mollie->customers->all(0, 1)->data[0];

	/*
	 * Customer Payment creation parameters.
	 *
	 * See: https://www.mollie.com/en/docs/reference/customers/create-payment
	 */
	$payment = $mollie->customers_payments->with($customer)->create(array(
		"amount"        => 10.00,
		"description"   => "An on-demand recurring payment",

		// Flag this payment as a recurring payment.
		"recurringType" => Mollie_API_Object_Payment::RECURRINGTYPE_RECURRING,
	));

	/*
	 * The payment will be either pending or paid immediately. The customer
	 * does not have to perform any payment steps.
	 */

	echo "<p>Selected mandate is '" . htmlspecialchars($payment->mandateId) . "' (" . htmlspecialchars($payment->method) . ").</p>\n";
	echo "<p>The payment status is '" . htmlspecialchars($payment->status) . "'.</p>\n";
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
