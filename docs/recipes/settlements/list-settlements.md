# List Settlements

How to list settlements using the Mollie API.

## List Settlements

```php
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;

try {
    // List all settlements
    $response = $mollie->send(new GetPaginatedSettlementsRequest);

    foreach ($response as $settlement) {
        echo "Settlement {$settlement->reference}:\n";
        echo "- Created: {$settlement->createdAt}\n";
        echo "- Status: {$settlement->status}\n";
        echo "- Amount: {$settlement->amount->currency} {$settlement->amount->value}\n\n";

        // Show settlement periods
        foreach ($settlement->periods as $year => $months) {
            foreach ($months as $month => $data) {
                echo "Period {$year}-{$month}:\n";

                // Show revenue
                foreach ($data->revenue as $revenue) {
                    echo "Revenue: {$revenue->description}\n";
                    echo "- Count: {$revenue->count}\n";
                    echo "- Net: {$revenue->amountNet->currency} {$revenue->amountNet->value}\n";
                    echo "- VAT: {$revenue->amountVat->currency} {$revenue->amountVat->value}\n";
                    echo "- Gross: {$revenue->amountGross->currency} {$revenue->amountGross->value}\n\n";
                }

                // Show costs
                foreach ($data->costs as $cost) {
                    echo "Cost: {$cost->description}\n";
                    echo "- Count: {$cost->count}\n";
                    echo "- Net: {$cost->amountNet->currency} {$cost->amountNet->value}\n";
                    echo "- VAT: {$cost->amountVat->currency} {$cost->amountVat->value}\n";
                    echo "- Gross: {$cost->amountGross->currency} {$cost->amountGross->value}\n\n";
                }
            }
        }
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$settlement->id;          // "stl_abc123"
$settlement->reference;   // "1234567.1804.03"
$settlement->createdAt;   // "2024-02-24T12:13:14+00:00"
$settlement->settledAt;   // "2024-02-24T12:13:14+00:00"
$settlement->status;      // "open", "pending", "paidout", "failed"
$settlement->amount;      // Object containing amount and currency
$settlement->periods;     // Object containing settlement periods
```

## Additional Notes

- OAuth access token is required to access settlements
- Settlements contain revenue and costs grouped by period
- Each period shows transaction fees, refunds, and chargebacks
