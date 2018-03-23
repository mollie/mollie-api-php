<?php
/*
 * Example 5 - How to retrieve your payments history.
 */

use Mollie\Api\Exceptions\ApiException;

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/settings/profiles
     */
    require "initialize.php";


    /*
     * Get the all payments for this API key ordered by newest.
     */
    $payments = $mollie->payments->all();

    foreach ($payments as $payment) {
        echo "&euro; " . htmlspecialchars($payment->amount) . ", status: " . htmlspecialchars($payment->status) . "<br>";
    }
} catch (ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
