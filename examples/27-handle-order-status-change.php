<?php
/*
 * Example 27 - Handle an order status change using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "./initialize.php";

    /*
     * After your webhook has been called with the order ID in its body, you'd like
     * to handle the order's status change. This is how you can do that.
     *
     * See: https://docs.mollie.com/reference/v2/orders-api/get-order
     */
    $order = $mollie->orders->get("ord_pbjz8x");

    if ($order->isPaid() || $order->isAuthorized()) {
        echo "The payment for your order " . $order->id . " has been processed.";
        echo "\nYour order is now being prepared for shipment.";
    } elseif ($order->isCanceled()) {
        echo "Your order " . $order->id . " has been canceled.";
    } elseif ($order->isRefunded()) {
        echo "Your order " . $order->id . " has been refunded.";
    } elseif ($order->isExpired()) {
        echo "Your order " . $order->id . " has expired.";
    } elseif ($order->isCompleted()) {
        echo "Your order " . $order->id . " is completed.";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
