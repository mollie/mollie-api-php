<?php
/*
 * List sales invoices using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * List the most recent sales invoices
     *
     * See: https://docs.mollie.com/reference/v2/sales-invoices-api/list-sales-invoices
     */
    echo '<ul>';
    $salesInvoices = $mollie->salesInvoices->page();
    foreach ($salesInvoices as $invoice) {
        echo '<li><b>Invoice ' . htmlspecialchars($invoice->id) . ':</b> (' . htmlspecialchars($invoice->issuedAt) . ')';
        echo '<br>Status: <b>' . htmlspecialchars($invoice->status) . '</b>';
        echo '<br>Total Amount: <b>' . htmlspecialchars($invoice->amount->currency) . ' ' . htmlspecialchars($invoice->amount->value) . '</b>';
        echo '</li>';
    }
    echo '</ul>';
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
