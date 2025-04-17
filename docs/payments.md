##### Multicurrency #####
Since API v2.0 it is now possible to create non-EUR payments for your customers.
A full list of available currencies can be found [in our documentation](https://docs.mollie.com/guides/multicurrency).

```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "USD",
        "value" => "10.00"
    ],
    //...
]);
```
_After creation, the `settlementAmount` will contain the EUR amount that will be settled on your account._

##### Create fully integrated iDEAL payments #####
To fully integrate iDEAL payments on your website, follow these additional steps:

1. Retrieve the list of issuers (banks) that support iDEAL.

```php
$method = $mollie->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL, ["include" => "issuers"]);
```

Use the `$method->issuers` list to let the customer pick their preferred issuer.

_`$method->issuers` will be a list of objects. Use the property `$id` of this object in the
 API call, and the property `$name` for displaying the issuer to your customer._

2. Create a payment with the selected issuer:

```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
    "method"      => \Mollie\Api\Types\PaymentMethod::IDEAL,
    "issuer"      => $selectedIssuerId, // e.g. "ideal_INGBNL2A"
]);
```

_The `_links` property of the `$payment` object will contain an object `checkout` with a `href` property, which is a URL that points directly to the online banking environment of the selected issuer.
A short way of retrieving this URL can be achieved by using the `$payment->getCheckoutUrl()`._

For a more in-depth example, see [Example - iDEAL payment](./recipes/payments/create-ideal-payment.md).

#### Retrieving Payments ####
**[Retrieve Payment Documentation](https://docs.mollie.com/reference/v2/payments-api/get-payment)**

We can use the `$payment->id` to retrieve a payment and check if the payment `isPaid`.

```php
$payment = $mollie->payments->get($payment->id);

if ($payment->isPaid())
{
    echo "Payment received.";
}
```

Or retrieve a collection of payments.

```php
$payments = $mollie->payments->page();
```

For an extensive example of listing payments with the details and status, see [Example - List Payments](./recipes/payments/list-payments.md).

#### Refunding payments ####
**[Refund Payment Documentation](https://docs.mollie.com/reference/v2/refunds-api/create-payment-refund)**

Our API provides support for refunding payments. It's important to note that there is no confirmation step, and all refunds are immediate and final. Refunds are available for all payment methods except for paysafecard and gift cards.

```php
$payment = $mollie->payments->get($payment->id);

// Refund € 2 of this payment
$refund = $payment->refund([
    "amount" => [
        "currency" => "EUR",
        "value" => "2.00"
    ]
]);
```

#### Payment webhook ####
When the payment status changes, the `webhookUrl` you specified during payment creation will be called. You can use the `id` from the POST parameters to check the status and take appropriate actions.
For more details, refer to [Example - Webhook](./recipes/payments/handle-webhook.md).

For a working example, see [Example - Refund payment](./recipes/payments/refund-payment.md).
