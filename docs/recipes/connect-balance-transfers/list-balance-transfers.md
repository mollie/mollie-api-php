# List Connect Balance Transfers

How to retrieve a list of all connect balance transfers using the Mollie API.

## The Code

```php
use Mollie\Api\Http\Requests\ListConnectBalanceTransfersRequest;

try {
    // Initialize the Mollie client with your OAuth access token
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // List all balance transfers
    $balanceTransfers = $mollie->send(
        new ListConnectBalanceTransfersRequest()
    );

    foreach ($balanceTransfers as $transfer) {
        echo "Transfer {$transfer->id}:\n";
        echo "- Amount: {$transfer->amount->currency} {$transfer->amount->value}\n";
        echo "- Description: {$transfer->description}\n";
        echo "- From: {$transfer->source->balanceId}\n";
        echo "- To: {$transfer->destination->balanceId}\n";
        echo "- Created: {$transfer->createdAt}\n\n";
    }

    // Get the next page if available
    if ($balanceTransfers->hasNext()) {
        $nextPage = $balanceTransfers->next();
        // Process next page...
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## With Pagination Parameters

```php
use Mollie\Api\Http\Requests\ListConnectBalanceTransfersRequest;

try {
    // List balance transfers with pagination
    $balanceTransfers = $mollie->send(
        new ListConnectBalanceTransfersRequest(
            from: 'cbt_8KhHNOSdpL',  // Start from this ID
            limit: 50,                // Limit to 50 results
            sort: 'createdAt'         // Sort by creation date
        )
    );

    foreach ($balanceTransfers as $transfer) {
        // Process each transfer...
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Using Iterator for All Transfers

```php
use Mollie\Api\Http\Requests\ListConnectBalanceTransfersRequest;

try {
    // Use iterator to automatically handle pagination
    $request = new ListConnectBalanceTransfersRequest();

    $allTransfers = $mollie->send($request->useIterator());

    foreach ($allTransfers as $transfer) {
        echo "Transfer {$transfer->id}: ";
        echo "{$transfer->amount->currency} {$transfer->amount->value}\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Using Endpoint Collections (Legacy Style)

```php
try {
    // List balance transfers using endpoint collections
    $balanceTransfers = $mollie->connectBalanceTransfers->page();

    foreach ($balanceTransfers as $transfer) {
        echo "Transfer: {$transfer->description}\n";
        echo "Amount: {$transfer->amount->value}\n\n";
    }

    // Use iterator for automatic pagination
    foreach ($mollie->connectBalanceTransfers->iterator() as $transfer) {
        // Process each transfer across all pages...
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
// Collection properties
$balanceTransfers->count;    // Number of transfers in current page
$balanceTransfers->_links;   // Pagination links

// Individual transfer properties
$transfer->id;                      // "cbt_4KgGJJSZpH"
$transfer->resource;               // "connect-balance-transfer"
$transfer->amount->currency;       // "EUR"
$transfer->amount->value;          // "100.00"
$transfer->description;            // "Transfer from balance A to balance B"
$transfer->status;                 // "created", "failed", "succeeded"
$transfer->category;               // "manual_correction", "purchase", "refund", etc.
$transfer->source->type;           // "organization"
$transfer->source->id;             // "org_12345678"
$transfer->source->description;    // "Payment from Organization A"
$transfer->destination->type;      // "organization"
$transfer->destination->id;        // "org_87654321"
$transfer->destination->description; // "Payment to Organization B"
$transfer->executedAt;             // "2023-12-25T10:31:00+00:00" (null if not executed)
$transfer->mode;                   // "live" or "test"
$transfer->createdAt;              // "2023-12-25T10:30:54+00:00"
```

## Pagination Methods

```php
// Check if there's a next page
if ($balanceTransfers->hasNext()) {
    $nextPage = $balanceTransfers->next();
}

// Check if there's a previous page
if ($balanceTransfers->hasPrevious()) {
    $previousPage = $balanceTransfers->previous();
}

// Get auto-iterator for all transfers
$iterator = $balanceTransfers->getAutoIterator();
foreach ($iterator as $transfer) {
    // Processes all transfers across all pages automatically
}
```

## Additional Notes

- **OAuth Required**: You need an OAuth access token to list balance transfers
- **Access Scope**: The list includes only transfers involving balances accessible through your OAuth app
- **Pagination**:
  - Results are paginated with a default limit
  - Use `from` parameter to start from a specific transfer ID
  - Use `limit` to control the number of results per page (max 250)
- **Sorting**:
  - Transfers are returned in descending order by creation date (newest first)
  - Use the `sort` parameter to customize ordering
- **Filtering**: Currently, there are no filter parameters available. All accessible transfers are returned.
- **Performance**:
  - Use the iterator for processing large numbers of transfers
  - The iterator automatically handles pagination
  - Set appropriate limits to balance between API calls and memory usage
- **Iteration Direction**:
  - By default, iteration goes forward (newest to oldest)
  - Set `iterateBackwards: true` to reverse the direction
- **Use Cases**:
  - Financial reporting and reconciliation
  - Audit trails for balance movements
  - Analyzing transfer patterns
  - Monitoring platform activity

## Related Resources

- [Create Connect Balance Transfer](create-balance-transfer.md)
- [Get Connect Balance Transfer](get-balance-transfer.md)
