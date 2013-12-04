<?php
/*
 * Example 3 - How to show a return page to the customer.
 *
 * In this example we retrieve the order stored in the database.
 * Here, it's unnecessary to use the Mollie API Client.
 */
$status = database_read($_GET["order_id"]);

echo "<p>Your payment status is '" . htmlspecialchars($status) . "'.</p>";
echo "<p>";
echo '<a href="' . dirname($_SERVER["SCRIPT_URI"]) . '/1-new-payment.php">Retry example 1</a><br>';
echo '<a href="' . dirname($_SERVER["SCRIPT_URI"]) . '/4-ideal-payment.php">Retry example 4</a><br>';
echo "</p>";


/*
 * NOTE: This example uses a text file as a database. Please use a real database like MySQL in production code.
 */
function database_read ($order_id)
{
	$order_id = intval($order_id);
	$database = dirname(__FILE__) . "/orders/order-{$order_id}.txt";

	$status  = @file_get_contents($database);

	return $status ? $status : "unknown order";
}
