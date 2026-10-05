# Background jobs and queues

Queue business data or resource IDs, then resolve an authenticated `MollieApiClient` from your worker's container. Obtain credentials in the worker; do not include them in a job payload.

```php
// Job payload: ['paymentId' => 'tr_example']
function processPaymentJob(array $job, Mollie\Api\MollieApiClient $mollie): void
{
    // The worker supplies a freshly configured, authenticated client.
    $payment = $mollie->payments->get($job['paymentId']);
    // Update local business state using the payment.
}
```

Do not assume an SDK client or HTTP transport can be serialized for a queue or cache. SDK serialization excludes the Mollie authenticator but retains the transport. Real Guzzle clients may contain closures or reject serialization, and injected transport configuration may contain credentials. Restore custom handlers, TLS, proxy and authentication settings through the worker's normal client configuration.
