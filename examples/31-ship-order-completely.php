<?php
/*
 * Example 31 - Create a shipment for an entire order using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "./initialize.php";

    /*
     * Create a shipment for the entire order with ID "ord_8wmqcHMN4U"
     *
     * See: https://docs.mollie.com/reference/v2/shipments-api/create-shipment
     */

    $order = $this->getOrder('ord_8wmqcHMN4U');
    $shipment = $order->shipAll();

    echo 'A shipment with ID ' . $shipment->id. ' has been created for your order with ID ' . $order->id . '.';
    foreach ($shipment->lines as $line) {
        echo $line->description . '. Status: <b>' . $line->status . '</b>.';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
