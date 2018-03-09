<?php
/*
 * Example 11 - How to create a new customer in the Mollie API.
 */

use Mollie\Api\Exceptions\ApiException;

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "initialize.php";

    /*
     * Customer creation parameters.
     *
     * See: https://www.mollie.com/en/docs/reference/customers/create
     */
    $customer = $mollie->customers->create(array(
        "name" => "Luke Skywalker",
        "email" => "luke@example.org",
        "metadata" => array(
            "isJedi" => TRUE,
        ),
    ));

    echo "<p>New customer created " . htmlspecialchars($customer->id) . " (" . htmlspecialchars($customer->name) . ").</p>";
} catch (ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
