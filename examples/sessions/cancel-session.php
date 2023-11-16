<?php
/*
 * Cancel an session using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Cancel the session with ID "sess_dfsklg13jO"
     *
     * See: https://docs.mollie.com/reference/v2/sessions-api/cancel-session
     */
    $session = $mollie->sessions->get("sess_dfsklg13jO");

    $session->cancel();
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
