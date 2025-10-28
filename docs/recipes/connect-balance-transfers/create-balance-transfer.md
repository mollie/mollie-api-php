# Create a Connect Balance Transfer

How to create a balance transfer between two connected Mollie balances using the API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\TransferParty;
use Mollie\Api\Http\Requests\CreateConnectBalanceTransferRequest;
use Mollie\Api\Types\ConnectBalanceTransferCategory;

try {
    // Initialize the Mollie client with your OAuth access token
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // Create a balance transfer
    $balanceTransfer = $mollie->send(
        new CreateConnectBalanceTransferRequest(
            amount: new Money(
                currency: 'EUR',
                value: '100.00'
            ),
            description: 'Transfer from balance A to balance B',
            source: new TransferParty(
                id: 'org_12345678',
                description: 'Payment from Organization A'
            ),
            destination: new TransferParty(
                id: 'org_87654321',
                description: 'Payment to Organization B'
            ),
            category: ConnectBalanceTransferCategory::MANUAL_CORRECTION
        )
    );

    echo "Balance transfer created: {$balanceTransfer->id}\n";
    echo "Amount: {$balanceTransfer->amount->currency} {$balanceTransfer->amount->value}\n";
    echo "Status: Transfer initiated\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Using Endpoint Collections (Legacy Style)

```php
try {
    // Create a balance transfer using endpoint collections
    $balanceTransfer = $mollie->connectBalanceTransfers->create([
        'amount' => [
            'currency' => 'EUR',
            'value' => '100.00'
        ],
        'description' => 'Transfer from balance A to balance B',
        'source' => [
            'type' => 'organization',
            'id' => 'org_12345678',
            'description' => 'Payment from Organization A'
        ],
        'destination' => [
            'type' => 'organization',
            'id' => 'org_87654321',
            'description' => 'Payment to Organization B'
        ],
        'category' => 'manual_correction'
    ]);

    echo "Balance transfer created: {$balanceTransfer->id}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$balanceTransfer->id;                      // "cbt_4KgGJJSZpH"
$balanceTransfer->resource;               // "connect-balance-transfer"
$balanceTransfer->amount->currency;       // "EUR"
$balanceTransfer->amount->value;          // "100.00"
$balanceTransfer->description;            // "Transfer from balance A to balance B"
$balanceTransfer->status;                 // "created", "failed", "succeeded"
$balanceTransfer->statusReason;           // Object with status reason (if applicable)
$balanceTransfer->category;               // "manual_correction", "purchase", "refund", etc.
$balanceTransfer->source->type;           // "organization"
$balanceTransfer->source->id;             // "org_12345678"
$balanceTransfer->source->description;    // "Payment from Organization A"
$balanceTransfer->destination->type;      // "organization"
$balanceTransfer->destination->id;        // "org_87654321"
$balanceTransfer->destination->description; // "Payment to Organization B"
$balanceTransfer->executedAt;             // "2023-12-25T10:31:00+00:00" (null if not executed)
$balanceTransfer->mode;                   // "live" or "test"
$balanceTransfer->createdAt;              // "2023-12-25T10:30:54+00:00"
```

## Transfer Status

The transfer status indicates the current state of the transfer:

```php
// Check transfer status
if ($balanceTransfer->status === 'succeeded') {
    echo "Transfer completed successfully\n";
    echo "Executed at: {$balanceTransfer->executedAt}\n";
} elseif ($balanceTransfer->status === 'failed') {
    echo "Transfer failed\n";
    if ($balanceTransfer->statusReason) {
        echo "Reason: " . json_encode($balanceTransfer->statusReason) . "\n";
    }
}
```

## Transfer Categories

Different transfer categories may have different fees:

- `invoice_collection` - Collecting invoice payments
- `purchase` - Purchase-related transfers
- `chargeback` - Chargeback transfers
- `refund` - Refund transfers
- `service_penalty` - Service penalty fees
- `discount_compensation` - Discount compensations
- `manual_correction` - Manual corrections
- `other_fee` - Other fees

## Additional Notes

- **OAuth Required**: You need an OAuth access token to create balance transfers. API keys are not supported for this endpoint.
- **Sub-merchant consent**: You must have a documented consent from your sub-merchants authorizing balance movements.
- **Balance Ownership**: You can only transfer funds between balances that belong to organizations connected through your OAuth app.
- **Transfer Speed**: Balance transfers are processed immediately. The funds are moved instantly between the balances.
- **Status Tracking**: Monitor the `status` field to track transfer completion. Check `executedAt` for the exact completion time.
- **Currency Requirements**:
  - Both balances must use the same currency
  - The transfer amount must match the balance currency
- **Balance Validation**:
  - The source balance must have sufficient available funds
  - Both balances must be active and operational
- **Use Cases**:
  - Moving funds between merchant balances in a marketplace
  - Redistributing funds across different connected accounts
  - Managing liquidity between multiple balances
- **Permissions**: Your OAuth app must have the appropriate scopes to access and transfer between the balances
- **Idempotency**: Consider using idempotency keys when creating transfers to prevent duplicate transfers in case of network issues

## Related Resources

- [List Connect Balance Transfers](list-balance-transfers.md)
- [Get Connect Balance Transfer](get-balance-transfer.md)
