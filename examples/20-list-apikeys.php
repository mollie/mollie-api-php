<?php
/*
 * Example 20 - How to get the API keys for a given profile
 */

try
{
    /*
     * Initialize the Mollie API library with your OAuth access token.
     */
    include "initialize_with_oauth.php";

    /*
     * Retrieve an existing profile by his profileId
     */
    $profile = $mollie->profiles->get("pfl_eB5MZz7Cvy");

    /*
     * Get the API keys for this profile.
     */
    $api_keys = $mollie->profiles_apikeys->with($profile)->all();

    foreach ($api_keys as $api_key)
    {
        echo htmlspecialchars($api_key->key) . "<br />";
    }
}
catch (Mollie_API_Exception $e)
{
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
