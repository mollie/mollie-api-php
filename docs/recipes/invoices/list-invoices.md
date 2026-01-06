# List Mollie Invoices

How to retrieve your Mollie invoices using the API.

## The Code

```php
use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;

try {
    // Initialize with OAuth (required for invoices)
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // Get all invoices
    $response = $mollie->send(
        new GetPaginatedInvoiceRequest()
    );

    foreach ($response as $invoice) {
        echo "Invoice {$invoice->reference}:\n";
        echo "- Status: {$invoice->status}\n";
        echo "- Issued: {$invoice->issuedAt}\n";
        echo "- Paid: {$invoice->paidAt}\n";
        echo "- Due: {$invoice->dueAt}\n\n";

        echo "Lines:\n";
        foreach ($invoice->lines as $line) {
            echo "- {$line->description}\n";
            echo "  Period: {$line->period}\n";
            echo "  Count: {$line->count}\n";
            echo "  VAT: {$line->vatPercentage}%\n";
            echo "  Amount: {$line->amount->currency} {$line->amount->value}\n\n";
        }

        echo "Totals:\n";
        echo "- Net: {$invoice->netAmount->currency} {$invoice->netAmount->value}\n";
        echo "- VAT: {$invoice->vatAmount->currency} {$invoice->vatAmount->value}\n";
        echo "- Gross: {$invoice->grossAmount->currency} {$invoice->grossAmount->value}\n\n";

        echo "PDF: {$invoice->_links->pdf->href}\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$invoice->id;                  // "inv_xBEbP9rvAq"
$invoice->reference;          // "2024.10000"
$invoice->vatNumber;         // "NL123456789B01"
$invoice->status;            // "paid", "open"
$invoice->issuedAt;          // "2024-02-24"
$invoice->paidAt;            // "2024-02-24"
$invoice->dueAt;             // "2024-03-24"
$invoice->netAmount;         // Object containing amount excluding VAT
$invoice->vatAmount;         // Object containing VAT amount
$invoice->grossAmount;       // Object containing amount including VAT
$invoice->lines;             // Array of invoice lines
$invoice->_links->pdf->href; // URL to download PDF invoice
```

## Invoice Line Details

```php
$line->period;         // "2024-01"
$line->description;    // "iDEAL transaction fees"
$line->count;          // 1337
$line->vatPercentage; // "21.00"
$line->amount;        // Object containing line amount
```

## Additional Notes

- OAuth access token is required to access invoices
- Invoices are generated monthly for your Mollie account
- Each invoice line represents a different type of fee:
  - Transaction fees per payment method
  - Refund fees
  - Chargeback fees
  - Other service fees
- The PDF invoice is available through the `_links.pdf.href` URL
