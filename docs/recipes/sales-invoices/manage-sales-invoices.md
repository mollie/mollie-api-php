# Manage Sales Invoices

How to list and manage sales invoices using the Mollie API.

## List Sales Invoices

```php
use Mollie\Api\Http\Requests\GetPaginatedSalesInvoicesRequest;
use Mollie\Api\Types\SalesInvoiceStatus;

try {
    // List all sales invoices
    $response = $mollie->send(new GetPaginatedSalesInvoicesRequest);

    foreach ($response as $invoice) {
        $status = $invoice->status instanceof SalesInvoiceStatus
            ? $invoice->status->value
            : $invoice->status;

        echo "Invoice {$invoice->id}:\n";
        echo "- Number: " . ($invoice->invoiceNumber ?? 'draft') . "\n";
        echo "- Status: {$status}\n";
        echo "- Issued: " . ($invoice->issuedAt ?? 'not issued') . "\n";
        echo "- Amount due: {$invoice->amountDue->currency} {$invoice->amountDue->value}\n";
        if (isset($invoice->_links->pdfLink->href)) {
            echo "- PDF: {$invoice->_links->pdfLink->href}\n";
        }

        echo "\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$invoice->id;                    // "invoice_4Y0eZitmBnQ6IDoMqZQKh"
$invoice->invoiceNumber;         // Invoice number, or null while still a draft
$invoice->status;                // SalesInvoiceStatus case, or an unknown raw string
$invoice->issuedAt;              // Issue date, or null when not issued yet
$invoice->dueAt;                 // Due date, or null when not due yet
$invoice->amountDue;             // Money object containing the outstanding amount
$invoice->subtotalAmount;        // Money object containing the subtotal
$invoice->totalVatAmount;        // Money object containing the VAT total
$invoice->totalAmount;           // Money object containing the invoice total
$invoice->lines;                 // Array of invoice lines, or null when omitted
$invoice->_links->pdfLink->href; // URL to download the PDF invoice
```

## Additional Notes

- OAuth access token is required to access sales invoices
- Sales invoices are generated for your Mollie account
- Each invoice line represents a different type of fee
- The PDF invoice is available through the `_links.pdfLink.href` URL
