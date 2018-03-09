<?php
/*
Example 23 - Delete a customer from mollie api.
*/

use Mollie\Api\Exceptions\ApiException;

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "initialize.php";

    $mollie->customers->delete("customer_id");
    echo "Customer deleted!";

} catch (ApiException $e) {
    error_log("API call failed: " . htmlspecialchars($e->getMessage()));
}

