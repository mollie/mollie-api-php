# Create a Connect Balance Transfer

How to create a balance transfer between two connected Mollie balances using the API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateConnectBalanceTransferRequest;

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
            sourceBalanceId: 'bal_gVMhHKqSSRYJyPsuoPABC',
            destinationBalanceId: 'bal_gVMhHKqSSRYJyPsuoPXYZ'
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
            'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPABC'
        ],
        'destination' => [
            'balanceId' => 'bal_gVMhHKqSSRYJyPsuoPXYZ'
        ]
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
$balanceTransfer->source->balanceId;      // "bal_gVMhHKqSSRYJyPsuoPABC"
$balanceTransfer->destination->balanceId; // "bal_gVMhHKqSSRYJyPsuoPXYZ"
$balanceTransfer->createdAt;              // "2023-12-25T10:30:54+00:00"
```

## Additional Notes

- **OAuth Required**: You need an OAuth access token to create balance transfers. API keys are not supported for this endpoint.
- **Balance Ownership**: You can only transfer funds between balances that belong to organizations connected through your OAuth app.
- **Transfer Speed**: Balance transfers are processed immediately. The funds are moved instantly between the balances.
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
