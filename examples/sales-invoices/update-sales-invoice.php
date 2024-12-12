<?php
/*
 * Update a sales invoice using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Assume we have an invoice ID 'inv_xxx' that we wish to update.
     */
    $invoiceId = 'invoice_xxx';

    /*
     * Update the sales invoice
     */
    $updatedInvoice = $mollie->salesInvoices->update($invoiceId, [
        'status' => \Mollie\Api\Types\SalesInvoiceStatus::PAID,
        'recipientIdentifier' => 'XXXXX',
        'lines' => [
            [
                'id' => 'line_001',
                'description' => 'Updated subscription fee',
                'quantity' => 2,
                'vatRate' => '21',
                'unitPrice' => [
                    'currency' => 'EUR',
                    'value' => '15.00',
                ],
            ],
        ],
    ]);

    echo "<p>Sales invoice updated with ID: " . htmlspecialchars($updatedInvoice->id) . "</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
