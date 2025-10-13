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
$balanceTransfer->_links;                 // Object containing relevant URLs
```

## Transfer Party Details

Access the transfer party information:

```php
// Source party details
echo "Source Organization:\n";
echo "- Type: {$balanceTransfer->source->type}\n";  // "organization"
echo "- ID: {$balanceTransfer->source->id}\n";      // Organization token
echo "- Description: {$balanceTransfer->source->description}\n";

// Destination party details
echo "Destination Organization:\n";
echo "- Type: {$balanceTransfer->destination->type}\n";
echo "- ID: {$balanceTransfer->destination->id}\n";
echo "- Description: {$balanceTransfer->destination->description}\n";
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
