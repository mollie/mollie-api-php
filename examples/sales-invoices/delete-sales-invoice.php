<?php

/*
 * Delete a sales invoice using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require '../initialize.php';

    /*
     * Assume we have an invoice ID 'inv_xxx' that we wish to delete.
     */
    $invoiceId = 'invoice_xxx';

    /*
     * Delete the sales invoice
     */
    $mollie->salesInvoices->delete($invoiceId);

    echo '<p>Sales invoice deleted with ID: '.htmlspecialchars($invoiceId).'</p>';
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo 'API call failed: '.htmlspecialchars($e->getMessage());
}
