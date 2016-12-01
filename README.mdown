![Mollie](https://www.mollie.nl/files/Mollie-Logo-Style-Small.png) 

# Mollie API client for PHP #

Accepting [iDEAL](https://www.mollie.com/ideal/), [Bancontact/Mister Cash](https://www.mollie.com/mistercash/), [SOFORT Banking](https://www.mollie.com/sofort/), [Creditcard](https://www.mollie.com/creditcard/), [SEPA Bank transfer](https://www.mollie.com/banktransfer), [SEPA Direct debit](https://www.mollie.com/directdebit/), [Bitcoin](https://www.mollie.com/bitcoin/), [PayPal](https://www.mollie.com/paypal/), [Belfius Direct Net](https://www.mollie.com/belfiusdirectnet/), [KBC/CBC](https://www.mollie.com/kbccbc/) and [paysafecard](https://www.mollie.com/paysafecard/) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.

[![Build Status](https://travis-ci.org/mollie/mollie-api-php.png)](https://travis-ci.org/mollie/mollie-api-php)
[![Latest Stable Version](https://poser.pugx.org/mollie/mollie-api-php/v/stable)](https://packagist.org/packages/mollie/mollie-api-php)
[![Total Downloads](https://poser.pugx.org/mollie/mollie-api-php/downloads)](https://packagist.org/packages/mollie/mollie-api-php)

## Requirements ##
To use the Mollie API client, the following things are required:

+ Get yourself a free [Mollie account](https://www.mollie.com/aanmelden). No sign up costs.
+ Create a new [Website profile](https://www.mollie.com/beheer/account/profielen/) to generate API keys (live and test mode) and setup your webhook.
+ Now you're ready to use the Mollie API client in test mode.
+ In order to accept payments in live mode, payment methods must be activated in your account. Follow [a few of steps](https://www.mollie.com/beheer/diensten), and let us handle the rest.
+ PHP >= 5.3
+ PHP cURL extension
+ Up-to-date OpenSSL (or other SSL/TLS toolkit)
+ SSL v3 disabled. Mollie does not support SSL v3 anymore.

## Installation ##

By far the easiest way to install the Mollie API client is to require it with [Composer](http://getcomposer.org/doc/00-intro.md).

	$ composer require mollie/mollie-api-php:1.9.*

	    {
	        "require": {
	            "mollie/mollie-api-php": "1.9.*"
	        }
	    }

You may also git checkout or [download all the files](https://github.com/mollie/mollie-api-php/archive/master.zip), and include the Mollie API client manually.

## How to receive payments ##

To successfully receive a payment, these steps should be implemented:

1. Use the Mollie API client to create a payment with the requested amount, description and optionally, a payment method. It is important to specify a unique redirect URL where the customer is supposed to return to after the payment is completed.

2. Immediately after the payment is completed, our platform will send an asynchronous request to the configured webhook to allow the payment details to be retrieved, so you know when exactly to start processing the customer's order.

3. The customer returns, and should be satisfied to see that the order was paid and is now being processed.

## Getting started ##

Requiring the included autoloader. If you're using Composer, you can skip this step.

```php
	require "Mollie/API/Autoloader.php";
```
	
Initializing the Mollie API client, and setting your API key.

```php
	$mollie = new Mollie_API_Client;
	$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
```	

Creating a new payment.
	
```php
	$payment = $mollie->payments->create(array(
		"amount"      => 10.00,
		"description" => "My first API payment",
		"redirectUrl" => "https://webshop.example.org/order/12345/",
	));
```
	
_After creation, the payment id is available in the `$payment->id` property. You should store this id with your order._
	
Retrieving a payment.

```php
	$payment = $mollie->payments->get($payment->id);

	if ($payment->isPaid())
	{
		echo "Payment received.";
	}
```

### Fully integrated iDEAL payments ###

If you want to fully integrate iDEAL payments in your web site, some additional steps are required. First, you need to
retrieve the list of issuers (banks) that support iDEAL and have your customer pick the issuer he/she wants to use for
the payment.

Retrieve the list of issuers:

```php
	$issuers = $mollie->issuers->all();
```

_`$issuers` will be a list of `Mollie_API_Object_Issuer` objects. Use the property `$id` of this object in the
 API call, and the property `$name` for displaying the issuer to your customer. For a more in-depth example, see [Example 4](https://github.com/mollie/mollie-api-php/blob/master/examples/04-ideal-payment.php)._

Create a payment with the selected issuer:

```php
	$payment = $mollie->payments->create(array(
		"amount"      => 10.00,
		"description" => "My first API payment",
		"redirectUrl" => "https://webshop.example.org/order/12345/",
		"method" => Mollie_API_Object_Method::IDEAL,
		"issuer" => $selected_issuer_id, // e.g. "ideal_INGBNL2A"
	));
```

_The `links` property of the `$payment` object will contain a string `paymentUrl`, which is a URL that points directly to the online banking environment of the selected issuer._

### Refunding payments ###

The API also supports refunding payments. Note that there is no confirmation and that all refunds are immediate and
definitive. Refunds are only supported for iDEAL, credit card, Bancontact/Mister Cash, SOFORT Banking, PayPal, Belfius Direct Net and bank transfer payments. Other types of payments cannot
be refunded through our API at the moment.

```php
	$payment = $mollie->payments->get($payment->id);

	// Refund € 15 of this payment
	$refund = $mollie->payments->refund($payment, 15.00);
```

## How to use OAuth2 to connect Mollie accounts to your application? ##

The resources `permissions`, `organizations`, `refunds`, `profiles`, `settlements` and `invoices` are only available with an OAuth2 access token. This is because an API key is linked to a website profile, and those resources are linked to an Mollie account. Visit our [API documentation](https://www.mollie.com/en/docs/oauth/overview) for more information about how to get an OAuth2 access token. For an example of how to use those resources, see [Example 8](https://github.com/mollie/mollie-api-php/blob/master/examples/08-oauth-list-profiles.php), [Example 9](https://github.com/mollie/mollie-api-php/blob/master/examples/09-oauth-list-settlements.php) and [Example 10](https://github.com/mollie/mollie-api-php/blob/master/examples/10-oauth-new-payment.php).

## API documentation ##
If you wish to learn more about our API, please visit the [Mollie Developer Portal](https://www.mollie.com/developer/). API Documentation is available in both Dutch and English.

## Want to help us make our API client even better? ##

Want to help us make our API client even better? We take [pull requests](https://github.com/mollie/mollie-api-php/pulls?utf8=%E2%9C%93&q=is%3Apr), sure. But how would you like to contribute to a [technology oriented organization](https://www.mollie.com/nl/blog/post/werken-bij-mollie-als-developer/)? Mollie is hiring developers and system engineers. [Check out our vacancies](https://www.mollie.com/nl/jobs) or [get in touch](mailto:personeel@mollie.com).

## License ##
[BSD (Berkeley Software Distribution) License](http://www.opensource.org/licenses/bsd-license.php).
Copyright (c) 2013-2016, Mollie B.V.

## Support ##
Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com — +31 20 820 20 70

+ [More information about iDEAL via Mollie](https://www.mollie.com/ideal/)
+ [More information about credit card via Mollie](https://www.mollie.com/creditcard/)
+ [More information about Bancontact/Mister Cash via Mollie](https://www.mollie.com/mistercash/)
+ [More information about SOFORT Banking via Mollie](https://www.mollie.com/sofort/)
+ [More information about SEPA Bank transfer via Mollie](https://www.mollie.com/banktransfer/)
+ [More information about SEPA Direct debit via Mollie](https://www.mollie.com/directdebit/)
+ [More information about Bitcoin via Mollie](https://www.mollie.com/bitcoin/)
+ [More information about PayPal via Mollie](https://www.mollie.com/paypal/)
+ [More information about Belfius Direct Net via Mollie](https://www.mollie.com/belfiusdirectnet/)
+ [More information about paysafecard via Mollie](https://www.mollie.com/paysafecard/)
+ [More information about KBC/CBC via Mollie](https://www.mollie.com/kbccbc/)
