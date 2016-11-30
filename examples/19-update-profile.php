<?php
/*
 * Example 19 - Updating an existing profile via the Mollie API.
 */

try
{
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    include "initialize_with_oauth.php";

    /*
     * Retrieve an existing profile by his profileId
     */
    $profile = $mollie->profiles->get("pfl_eA4MSz7Bvy");

    /**
     * Profile fields that can be updated.
     *
     * @See https://www.mollie.com/en/docs/reference/profiles/update
     */
    $profile->name    = "Mollie B.V.";
    $profile->website = 'www.mollie.com';
    $profile->email   = 'info@mollie.com';
    $profile->phone   = '0612345670';
    $profile->categoryCode = 5399;
    $profile = $mollie->profiles->update($profile);

    echo "<p>Profile updated: " . htmlspecialchars($profile->name) . "</p>";
}
catch (Mollie_API_Exception $e)
{
    echo "<p>API call failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
