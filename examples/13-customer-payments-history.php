<?php
/*
 * Example 13 - How to retrieve your customers' payments history.
 */

try
{
	/*
	 * Initialize the Mollie API library with your API key.
	 *
	 * See: https://www.mollie.com/beheer/account/profielen/
	 */
	include "initialize.php";

	/**
	 * Retrieve the last created customer for this example.
	 * If no customers are created yet, run example 11.
	 */
	$customer = $mollie->customers->all(0, 1)->data[0];

	// Pagination
	$offset = 0;
	$limit  = 25;

	/*
	 * Get the all payments for this API key ordered by newest.
	 */
	$payments = $mollie->customers_payments->with($customer)->all($offset,  $limit);

	foreach ($payments as $payment)
	{
		echo "&euro; " . htmlspecialchars($payment->amount) . ", status: " . htmlspecialchars($payment->status) . "<br>";
	}
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
