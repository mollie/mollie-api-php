<?php
/*
 * Example 7 - How to refund a payment programmatically
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
     * Retrieve the payment you want to refund from the API.
     */
    $paymentId = "tr_q2cLW9pxMT";
    $payment = $mollie->payments->get($paymentId);

    // Check if this payment can be refunded
    // You can also check if the payment can be partially refunded
    // by using $payment->canBePartiallyRefunded() and $payment->getAmountRemaining()
    if ($payment->canBeRefunded()) {
        /*
         * Refund € 15,00 of the payment.
         *
         * https://www.mollie.com/en/docs/reference/refunds/create
         */
        $refund = $mollie->payments->refund($payment, [
            "amount" => [
                "currency" => "EUR",
                "value" => "15.00"
            ]
        ]);

        echo "€ 15,00 of payment {$paymentId} refunded.", PHP_EOL;
    } else {
        echo "Payment {$paymentId} can not be refunded.", PHP_EOL;
    }

    /*
     * Retrieve all refunds on a payment.
     */
    var_dump($mollie->paymentsRefunds->with($payment)->all());
} catch (ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
