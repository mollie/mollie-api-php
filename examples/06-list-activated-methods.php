<?php
/*
 * Example 6 - How to get the currently activated payment methods.
 */

use Mollie\Api\Exceptions\ApiException;

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/settings/profiles
     */
    require "./initialize.php";
    /*
     * Get all the activated methods for this API key.
     */
    $methods = $mollie->methods->all();
    foreach ($methods as $method) {
        echo '<div style="line-height:40px; vertical-align:top">';
        echo '<img src="' . htmlspecialchars($method->image->size1x) . '" srcset="' . htmlspecialchars($method->image->size2x) . ' 2x"> ';
        echo htmlspecialchars($method->description) . ' (' . htmlspecialchars($method->id) . ')';
        echo '</div>';
    }
} catch (ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
