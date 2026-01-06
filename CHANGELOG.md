# Changelog

Starting with v3, all notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/mollie/mollie-api-php/compare/v3.8.0...HEAD)

## [v3.8.0](https://github.com/mollie/mollie-api-php/compare/v3.7.0...v3.8.0) - 2026-01-06

### What's Changed

* feat: add metadata to connect balance transfer by @Naoray in https://github.com/mollie/mollie-api-php/pull/848
* Fix cURL deprecation notice for PHP 8.5 and higher by @RobinvanderVliet in https://github.com/mollie/mollie-api-php/pull/847
* fix: make TransferParty data accessible for debugging by @Naoray in https://github.com/mollie/mollie-api-php/pull/849
* Sandervanhooft fix/inclusion qr mismatch by @Naoray in https://github.com/mollie/mollie-api-php/pull/851

### New Contributors

* @RobinvanderVliet made their first contribution in https://github.com/mollie/mollie-api-php/pull/847

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.7.0...v3.8.0

## [v3.7.0](https://github.com/mollie/mollie-api-php/compare/v3.6.0...v3.7.0) - 2025-12-01

### What's Changed

* Add GOOGLEPAY and SWISH payment methods  by @samdejongobc in https://github.com/mollie/mollie-api-php/pull/844
* Add fromArray to Money by @Naoray in https://github.com/mollie/mollie-api-php/pull/846

### New Contributors

* @samdejongobc made their first contribution in https://github.com/mollie/mollie-api-php/pull/844

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.6.0...v3.7.0

## [v3.6.0](https://github.com/mollie/mollie-api-php/compare/v3.5.0...v3.6.0) - 2025-11-05

### What's Changed

* Feat/add balance transfer webhook events by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/842
* Fixed webhook docs typo and explained next-gen webhook focus by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/841

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.5.0...v3.6.0

## [v3.5.0](https://github.com/mollie/mollie-api-php/compare/v3.4.0...v3.5.0) - 2025-10-28

### Added

* Feat/add retry logic by @Naoray in https://github.com/mollie/mollie-api-php/pull/826
* Feat/add fake retain requests option by @Naoray in https://github.com/mollie/mollie-api-php/pull/830
* feat: add isEInvoice param and add support for testmode in all sales-… by @Naoray in https://github.com/mollie/mollie-api-php/pull/832
* feat: add customerId and mandateId to create sales invoice request by @Naoray in https://github.com/mollie/mollie-api-php/pull/834
* Feat/add balance transfer endpoint by @Naoray in https://github.com/mollie/mollie-api-php/pull/831
* Feat/add webhook mapping and events by @Naoray in https://github.com/mollie/mollie-api-php/pull/829
  - global Config that serves as a lookup map to easily map resources to their respective collection keys
  - `MockEvent` to easily test event handling
  - `Str` utility class
  - `classBasename` to `Utility`
  - `WebhookEntity` to serve as Container for Resource data received through webhooks (-> can be transformed into BaseResource)
  - Webhook Events that are instanced via the `WebhookEventMapper`
  

### Changed

- Feat/make sequence mock responses consume callables by @Naoray in https://github.com/mollie/mollie-api-php/pull/833

### Fixed

* Change include to embed just like in GetPaginatedChargebacksRequest.php #837 by @Naoray in https://github.com/mollie/mollie-api-php/pull/838
* Allow description on CreatePaymentRefundRequest to be empty by @Naoray in https://github.com/mollie/mollie-api-php/pull/839

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.4.0...v3.5.0

## [v3.4.0](https://github.com/mollie/mollie-api-php/compare/v3.3.3...v3.4.0) - 2025-08-13

### What's Changed

* Feat/add new payment route endpoints by @Naoray in https://github.com/mollie/mollie-api-php/pull/825

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.3.3...v3.4.0

## [v3.3.3](https://github.com/mollie/mollie-api-php/compare/v3.3.2...v3.3.3) - 2025-08-12

## What's Changed

* Fix/823 inconsistencies on payment link request by @Naoray in https://github.com/mollie/mollie-api-php/pull/824

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.3.2...v3.3.3

## [v3.3.2](https://github.com/mollie/mollie-api-php/compare/v3.3.1...v3.3.2) - 2025-07-25

## What's Changed

* Fix/819 signature date invalid by @Naoray in https://github.com/mollie/mollie-api-php/pull/820

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.3.1...v3.3.2

## [v3.3.1](https://github.com/mollie/mollie-api-php/compare/v3.3.0...v3.3.1) - 2025-07-25

### What's Changed

* fix: signature validator handling null signatures by @Naoray in https://github.com/mollie/mollie-api-php/pull/822

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.3.0...v3.3.1

## [v3.3.0](https://github.com/mollie/mollie-api-php/compare/v3.2.0...v3.3.0) - 2025-07-25

## What's Changed

* Feat/expose webhook signature header by @Naoray in https://github.com/mollie/mollie-api-php/pull/821
* Feat/expose webhook signature creation by @Naoray

## [v3.2.0](https://github.com/mollie/mollie-api-php/compare/v3.1.5...v3.2.0) - 2025-07-23

### What's Changed

* Feat/add create webhook endpoint by @Naoray in https://github.com/mollie/mollie-api-php/pull/812
* Feat/webhook signature verification by @Naoray in https://github.com/mollie/mollie-api-php/pull/813

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.1.5...v3.2.0

## [v3.1.5](https://github.com/mollie/mollie-api-php/compare/v3.1.4...v3.1.5) - 2025-07-10

### What's Changed

* Fix: allow array of payment methods when creating a payment by @jockri in https://github.com/mollie/mollie-api-php/pull/811
* Sandervanhooft fix/recipe classes by @Naoray in https://github.com/mollie/mollie-api-php/pull/815
* Fix class references on recipes by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/814
* Fix payment links expiresAt by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/817
* Change "include" into "embed" on GetPaginatedChargebacksRequest by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/818

### New Contributors

* @jockri made their first contribution in https://github.com/mollie/mollie-api-php/pull/811

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.1.4...v3.1.5

## [v3.1.4](https://github.com/mollie/mollie-api-php/compare/v3.1.3...v3.1.4) - 2025-06-11

## What's Changed

* Fix 400 Bad Request on DELETE when store array is empty by @cswiers in https://github.com/mollie/mollie-api-php/pull/810

## New Contributors

* @cswiers made their first contribution in https://github.com/mollie/mollie-api-php/pull/810

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.1.3...v3.1.4

## [v3.1.3](https://github.com/mollie/mollie-api-php/compare/v3.1.2...v3.1.3) - 2025-06-11

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.1.3...v3.1.3

## [v3.1.2](https://github.com/mollie/mollie-api-php/compare/v3.1.1...v3.1.2) - 2025-06-10

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.1.1...v3.1.2

## [v3.1.1](https://github.com/mollie/mollie-api-php/compare/v3.1.0...v3.1.1) - 2025-06-10

## What's Changed

* Fix/include resources by @Naoray in https://github.com/mollie/mollie-api-php/pull/808

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.1.0...v3.1.1

## [v3.1.0](https://github.com/mollie/mollie-api-php/compare/v3.0.6...v3.1.0) - 2025-06-05

### What's Changed

* Main by @Naoray in https://github.com/mollie/mollie-api-php/pull/804
* feat: add status reason to payment resource by @Naoray in https://github.com/mollie/mollie-api-php/pull/806

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.0.6...v3.1.0

## [v3.0.6](https://github.com/mollie/mollie-api-php/compare/v1.0.0-test...v3.0.6) - 2025-06-02

### What's Changed

* Amend capturable recipe by @fjbender in https://github.com/mollie/mollie-api-php/pull/796
* fix: exchange wrong request name by @Naoray in https://github.com/mollie/mollie-api-php/pull/797
* Removes nullability from delete() method, as it cannot return null by @Sjustein in https://github.com/mollie/mollie-api-php/pull/802
* fix: use payload instead of query params for testmode by @Naoray in https://github.com/mollie/mollie-api-php/pull/803

### New Contributors

* @Sjustein made their first contribution in https://github.com/mollie/mollie-api-php/pull/802

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.0.5...v3.0.6

## [v1.0.0-test](https://github.com/mollie/mollie-api-php/compare/v3.0.5...v1.0.0-test) - 2025-06-02

### What's Changed

* Amend capturable recipe by @fjbender in https://github.com/mollie/mollie-api-php/pull/796
* fix: exchange wrong request name by @Naoray in https://github.com/mollie/mollie-api-php/pull/797
* Removes nullability from delete() method, as it cannot return null by @Sjustein in https://github.com/mollie/mollie-api-php/pull/802
* fix: use payload instead of query params for testmode by @Naoray in https://github.com/mollie/mollie-api-php/pull/803

### New Contributors

* @Sjustein made their first contribution in https://github.com/mollie/mollie-api-php/pull/802

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.0.5...v1.0.0-test

## [v3.0.5](https://github.com/mollie/mollie-api-php/compare/v3.0.4...v3.0.5) - 2025-04-27

### What's Changed

* Fix/791 data types may mess up property order by @Naoray in https://github.com/mollie/mollie-api-php/pull/794

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.0.4...v3.0.5

## [v3.0.4](https://github.com/mollie/mollie-api-php/compare/v3.0.3...v3.0.4) - 2025-04-25

### What's Changed

* Chore/allow psr message v1 by @Naoray in https://github.com/mollie/mollie-api-php/pull/793
* Fix/789 remove overhault to resource calls by @Naoray in https://github.com/mollie/mollie-api-php/pull/792

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.0.3...v3.0.4

## [v3.0.3](https://github.com/mollie/mollie-api-php/compare/v3.0.2...v3.0.3) - 2025-04-23

### What's Changed

* Fixed docs links by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/787
* Feat/small improvements by @Naoray in https://github.com/mollie/mollie-api-php/pull/788
  * make `MockResponse` serializable
  * add changed `$metadata` handling to upgrade guide
  

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.0.2...v3.0.3

## [v3.0.2](https://github.com/mollie/mollie-api-php/compare/v3.0.0...v3.0.2) - 2025-04-17

### What's Changed

* handle nullable 422 exception field by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/786

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.0.1...v3.0.2
