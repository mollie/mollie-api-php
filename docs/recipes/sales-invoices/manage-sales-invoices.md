# Manage Sales Invoices

How to list and manage sales invoices using the Mollie API.

## List Sales Invoices

```php
use Mollie\Api\Http\Requests\GetPaginatedSalesInvoicesRequest;

try {
    // List all sales invoices
    $response = $mollie->send(new GetPaginatedSalesInvoicesRequest);

    foreach ($response as $invoice) {
        echo "Invoice {$invoice->reference}:\n";
        echo "- Status: {$invoice->status}\n";
        echo "- Issued: {$invoice->issuedAt}\n";
        echo "- Amount: {$invoice->amount->currency} {$invoice->amount->value}\n";
        echo "- PDF: {$invoice->_links->pdf->href}\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$invoice->id;           // "si_abc123"
$invoice->reference;    // "2024.0001"
$invoice->status;       // "paid", "open"
$invoice->issuedAt;     // "2024-02-24"
$invoice->paidAt;       // "2024-02-24" (optional)
$invoice->dueAt;        // "2024-03-24"
$invoice->amount;       // Object containing amount and currency
$invoice->netAmount;    // Object containing amount and currency (excluding VAT)
$invoice->vatAmount;    // Object containing amount and currency
$invoice->lines;        // Array of invoice lines
$invoice->_links;       // Object containing links (e.g., PDF download)
```

## Additional Notes

- OAuth access token is required to access sales invoices
- Sales invoices are generated for your Mollie account
- Each invoice line represents a different type of fee
- The PDF invoice is available through the `_links.pdf.href` URL
