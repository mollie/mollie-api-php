<?php
/*
 * Example 21 - Reset a API key
 */

try
{
    /*
     * Initialize the Mollie API library with a OAuth access token.
     */
    include "initialize_with_oauth.php";

    /*
     * Retrieve an existing profile by his profileId
     */
    $profile = $mollie->profiles->get("pfl_eA4NSz7Bvx");

    /*
     * Get the live API key for this profile.
     */
    $live_api_key = $mollie->profiles_apikeys->with($profile)->get('live');

    /*
     * Reset the live API key
     */
    $new_live_api_key = $mollie->profiles_apikeys->reset($live_api_key);
}
catch (Mollie_API_Exception $e)
{
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
