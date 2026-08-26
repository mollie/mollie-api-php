# List Mollie Invoices

How to retrieve your Mollie invoices using the API.

## The Code

```php
use Mollie\Api\Http\Requests\GetPaginatedInvoiceRequest;
use Mollie\Api\Types\InvoiceStatus;

try {
    // Initialize with OAuth (required for invoices)
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // Get all invoices
    $response = $mollie->send(
        new GetPaginatedInvoiceRequest()
    );

    foreach ($response as $invoice) {
        $status = $invoice->status instanceof InvoiceStatus
            ? $invoice->status->value
            : ($invoice->status ?? 'not available');

        echo "Invoice {$invoice->reference}:\n";
        echo "- Status: {$status}\n";
        echo "- Issued: " . ($invoice->issuedAt ?? 'not issued') . "\n";
        echo "- Paid: " . ($invoice->paidAt ?? 'not paid') . "\n";
        echo "- Due: " . ($invoice->dueAt ?? 'not available') . "\n\n";

        echo "Lines:\n";
        if ($invoice->lines !== null) {
            foreach ($invoice->lines as $line) {
                echo "- {$line->description}\n";
                echo "  Period: {$line->period}\n";
                echo "  Count: {$line->count}\n";
                echo "  VAT: {$line->vatPercentage}%\n";
                echo "  Amount: {$line->amount->currency} {$line->amount->value}\n\n";
            }
        }

        echo "Totals:\n";
        foreach ([
            'Net' => $invoice->netAmount,
            'VAT' => $invoice->vatAmount,
            'Gross' => $invoice->grossAmount,
        ] as $label => $amount) {
            if ($amount !== null) {
                echo "- {$label}: {$amount->currency} {$amount->value}\n";
            }
        }
        echo "\n";

        if (isset($invoice->_links->pdf->href)) {
            echo "PDF: {$invoice->_links->pdf->href}\n";
        }

        echo "\n";
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
$invoice->status;            // InvoiceStatus case, unknown raw string, or null
$invoice->issuedAt;          // Issue date, or null when omitted
$invoice->paidAt;            // Payment date, or null when unpaid
$invoice->dueAt;             // Due date, or null when omitted
$invoice->netAmount;         // Money object excluding VAT, or null
$invoice->vatAmount;         // VAT Money object, or null
$invoice->grossAmount;       // Money object including VAT, or null
$invoice->lines;             // Array of invoice lines, or null
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
