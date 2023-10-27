<?php
/*
 * How to use pagination with the Mollie API.
 *
 * pagination is supported on:
 * - balances
 * - balanceTransactions
 * - chargebacks
 * - clients
 * - clientLinks
 * - customers
 * - invoices
 * - mandates
 * - orders
 * - paymentCaptures
 * - paymentChargebacks
 * - payments
 * - paymentLinks
 * - paymentRefunds
 * - profiles
 * - refunds
 * - settlementCaptures
 * - settlementChargebacks
 * - settlementPayments
 * - settlementRefunds
 * - settlements
 * - subscriptions
 * - terminals
 */

try {
    /*
 * Initialize the Mollie API library with your API key or OAuth access token.
 */
    require "../initialize.php";


    // cursor paginating through all orders
    $page = $mollie->orders->page();

    while ($page->hasNext()) {
        foreach ($page as $order) {
            echo($order->id);
        }

        $page = $page->next();
    }


    // using the iterator we can iterate over all orders directly
    foreach ($mollie->orders->iterator() as $order) {
        echo($order->id);
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
