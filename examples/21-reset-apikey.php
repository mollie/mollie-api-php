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
    $profile = $mollie->profiles->get("pfl_eA4MSz7Bvy");

    /*
     * Reset the live API key
     */
    $new_live_api_key = $mollie->profiles_apikeys->with($profile)->reset("live");
}
catch (Mollie_API_Exception $e)
{
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
