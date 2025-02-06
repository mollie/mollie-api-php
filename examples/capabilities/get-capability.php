<?php

/*
 * Get a capability using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your OAuth access token.
     */
    require "../initialize_with_oauth.php";

    /*
     * Get the capability with ID "cap_1234567890"
     *
     * See: https://docs.mollie.com/reference/v2/capabilities-api/get-capability
     */
    $capability = $mollie->capabilities->get("payments");

    echo $capability->name;
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
