# List Settlements

How to list settlements using the Mollie API.

## List Settlements

```php
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;
use Mollie\Api\Types\SettlementStatus;

try {
    // List all settlements
    $response = $mollie->send(new GetPaginatedSettlementsRequest);

    foreach ($response as $settlement) {
        $status = $settlement->status instanceof SettlementStatus
            ? $settlement->status->value
            : ($settlement->status ?? 'not available');

        echo "Settlement {$settlement->reference}:\n";
        echo "- Created: " . ($settlement->createdAt ?? 'not available') . "\n";
        echo "- Status: {$status}\n";

        if ($settlement->amount !== null) {
            echo "- Amount: {$settlement->amount->currency} {$settlement->amount->value}\n";
        }

        echo "\n";

        if ($settlement->periods === null) {
            continue;
        }

        // Show settlement periods
        foreach ($settlement->periods as $year => $months) {
            foreach ($months as $month => $data) {
                echo "Period {$year}-{$month}:\n";

                // Show revenue
                foreach ($data->revenue as $revenue) {
                    echo "Revenue: {$revenue->description}\n";
                    echo "- Count: {$revenue->count}\n";
                    echo "- Net: {$revenue->amountNet->currency} {$revenue->amountNet->value}\n";
                    if (($vat = $revenue->amountVat ?? null) !== null) {
                        echo "- VAT: {$vat->currency} {$vat->value}\n";
                    }
                    echo "- Gross: {$revenue->amountGross->currency} {$revenue->amountGross->value}\n\n";
                }

                // Show costs
                foreach ($data->costs as $cost) {
                    echo "Cost: {$cost->description}\n";
                    echo "- Count: {$cost->count}\n";
                    echo "- Net: {$cost->amountNet->currency} {$cost->amountNet->value}\n";
                    if (($vat = $cost->amountVat ?? null) !== null) {
                        echo "- VAT: {$vat->currency} {$vat->value}\n";
                    }
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
$settlement->createdAt;   // Creation date, or null when omitted
$settlement->settledAt;   // Settlement date, or null while unsettled
$settlement->status;      // SettlementStatus case, unknown raw string, or null
$settlement->amount;      // Money object, or null when omitted
$settlement->periods;     // Settlement periods object, or null when omitted
```

## Additional Notes

- OAuth access token is required to access settlements
- Settlements contain revenue and costs grouped by period
- Each period shows transaction fees, refunds, and chargebacks
