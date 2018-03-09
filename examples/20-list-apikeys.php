<?php
/*
 * Example 20 - How to get the API keys for a given profile
 */

use Mollie\Api\Exceptions\ApiException;

try {
    /*
     * Initialize the Mollie API library with your OAuth access token.
     */
    require "initialize_with_oauth.php";

    /*
     * Retrieve an existing profile by his profileId
     */
    $profile = $mollie->profiles->get("pfl_eB5MZz7Cvy");

    /*
     * Get the API keys for this profile.
     */
    $api_keys = $mollie->apikeys->with($profile)->all();

    foreach ($api_keys as $api_key) {
        echo htmlspecialchars($api_key->key) . "<br />";
    }
} catch (ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
