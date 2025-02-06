<?php
/*
 * Using access token to list capabilities of an account.
 */
try {
    /*
     * Initialize the Mollie API library with your OAuth access token.
     */
    require "../initialize_with_oauth.php";

    /*
     * Get the all the capabilities for this account.
     */
    $capabilities = $mollie->capabilities->list();

    foreach ($capabilities as $capability) {
        echo '<div style="line-height:40px; vertical-align:top">';
        echo htmlspecialchars($capability->name) .
            ' - ' . htmlspecialchars($capability->status) .
            ' (' .  htmlspecialchars($capability->name) . ')';
        echo '</div>';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
