<?php
/*
 * Example 6 - How to get the currently activated payment methods.
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
	 * Get the all the activated methods for this API key.
	 */
	$methods = $mollie->methods->all();

	foreach ($methods as $method)
	{
		echo " * " . htmlspecialchars($method->id) . ": " . htmlspecialchars($method->description) . "<br>";
	}
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
