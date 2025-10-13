# Get a Connect Balance Transfer

How to retrieve details of a specific connect balance transfer using the Mollie API.

## The Code

```php
use Mollie\Api\Http\Requests\GetConnectBalanceTransferRequest;

try {
    // Initialize the Mollie client with your OAuth access token
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // Get a specific balance transfer
    $balanceTransfer = $mollie->send(
        new GetConnectBalanceTransferRequest(
            id: 'cbt_4KgGJJSZpH'
        )
    );

    echo "Balance Transfer {$balanceTransfer->id}:\n";
    echo "- Amount: {$balanceTransfer->amount->currency} {$balanceTransfer->amount->value}\n";
    echo "- Description: {$balanceTransfer->description}\n";
    echo "- From: {$balanceTransfer->source->balanceId}\n";
    echo "- To: {$balanceTransfer->destination->balanceId}\n";
    echo "- Created: {$balanceTransfer->createdAt}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Using Endpoint Collections (Legacy Style)

```php
try {
    // Get a balance transfer using endpoint collections
    $balanceTransfer = $mollie->connectBalanceTransfers->get('cbt_4KgGJJSZpH');

    echo "Transfer amount: {$balanceTransfer->amount->value}\n";
    echo "Transfer description: {$balanceTransfer->description}\n";
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
$balanceTransfer->source->balance;        // Object containing source balance details (optional)
$balanceTransfer->destination->balanceId; // "bal_gVMhHKqSSRYJyPsuoPXYZ"
$balanceTransfer->destination->balance;   // Object containing destination balance details (optional)
$balanceTransfer->createdAt;              // "2023-12-25T10:30:54+00:00"
$balanceTransfer->_links;                 // Object containing relevant URLs
```

## Balance Details

If balance details are embedded in the response, you can access them:

```php
// Source balance details (if embedded)
if (isset($balanceTransfer->source->balance)) {
    $sourceBalance = $balanceTransfer->source->balance;
    echo "Source Balance:\n";
    echo "- ID: {$sourceBalance->id}\n";
    echo "- Currency: {$sourceBalance->currency}\n";
    echo "- Status: {$sourceBalance->status}\n";
}

// Destination balance details (if embedded)
if (isset($balanceTransfer->destination->balance)) {
    $destBalance = $balanceTransfer->destination->balance;
    echo "Destination Balance:\n";
    echo "- ID: {$destBalance->id}\n";
    echo "- Currency: {$destBalance->currency}\n";
    echo "- Status: {$destBalance->status}\n";
}
```

## Additional Notes

- **OAuth Required**: You need an OAuth access token to retrieve balance transfer details
- **Access Control**: You can only retrieve transfers involving balances accessible through your OAuth app
- **Transfer Tracking**: Use the transfer ID to track and audit balance movements
- **Embedded Data**: The response may include embedded balance details for both source and destination balances
- **Links**: The `_links` object contains URLs to related resources:
  - `self`: Link to this balance transfer
  - `documentation`: Link to API documentation
- **Audit Trail**: Store transfer IDs in your system for reconciliation and reporting purposes
- **Error Handling**:
  - Returns 404 if the transfer ID doesn't exist
  - Returns 403 if you don't have permission to access this transfer

## Related Resources

- [Create Connect Balance Transfer](create-balance-transfer.md)
- [List Connect Balance Transfers](list-balance-transfers.md)
