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

    $orderId = 'ord_8wmqcHMN4U';

    // cursor paginating backwards through all orders
    $page = $mollie->orders->collect($orderId);

    while ($page->hasPrevious()) {
        foreach ($page as $order) {
            echo($order->id);
        }

        $page = $page->previous();
    }

    // iterating backwards using the iterator by passing iterateBackwards = true
    // in php 8.0+ you could also use the named parameter syntax iterator(iterateBackwards: true)
    foreach ($mollie->orders->iterator(null, null, [], true) as $order) {
        echo($order->id);
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
