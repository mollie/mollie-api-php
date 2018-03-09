<?php
/*
 * Example 21 - Reset a API key
 */

use Mollie\Api\Exceptions\ApiException;

try {
    /*
     * Initialize the Mollie API library with a OAuth access token.
     */
    require "initialize_with_oauth.php";

    /*
     * Retrieve an existing profile by his profileId
     */
    $profile = $mollie->profiles->get("pfl_eA4MSz7Bvy");

    /*
     * Reset the live API key
     */
    $new_live_api_key = $mollie->apikeys->with($profile)->reset("live");
} catch (ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
