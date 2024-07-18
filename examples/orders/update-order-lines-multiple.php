<?php
/*
 * Perform operations (add, cancel, update) on multiple order lines in a single call.
 */

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require "../initialize.php";

    /**
     * Perform multiple operations on Order Lines.
     *
     * See: https://docs.mollie.com/reference/v2/orders-api/manage-order-lines
     */

    $addOrderLine = [
        "operation" => \Mollie\Api\Types\OrderLineUpdateOperationType::ADD,
        "data" => [
            "type" => \Mollie\Api\Types\OrderLineType::DIGITAL,
            "name" => "Adding new orderline",
            "quantity" => 2,
            "sku" => "12345679",
            "totalAmount" => [
                "currency" => "EUR",
                "value" => "30.00",
            ],
            "unitPrice" => [
                "currency" => "EUR",
                "value" => "15.00",
            ],
            "vatAmount" => [
                "currency" => "EUR",
                "value" => "0.00",
            ],
            "vatRate" => "0.00",
        ],
    ];
    $updateOrderLine = [
        "operation" => \Mollie\Api\Types\OrderLineUpdateOperationType::UPDATE,
        "data" => [
            "id" => "odl_1.1l9vx0",
            "name" => "New order line name",
        ],
    ];
    $cancelOrderLine = [
        "operation" => \Mollie\Api\Types\OrderLineUpdateOperationType::CANCEL,
        "data" => [
            "id" => "odl_1.4hqjw6",
        ],
    ];

    $operations = [
        $addOrderLine,
        $updateOrderLine,
        $cancelOrderLine,
    ];

    $order = $mollie->orderLines->updateMultiple('ord_pbjz8x', $operations);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    /*
     * When updating order lines for orders that used a pay after delivery method such as Klarna Pay Later, the
     * supplier (Klarna) may decline the requested changes. This results in an error response from the Mollie API.
     * The order initial remains intact without applying the requested changes.
     */
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
