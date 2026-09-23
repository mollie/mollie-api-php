# Changelog

Starting with v3, all notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/mollie/mollie-api-php/compare/v3.15.0...HEAD)

## [v3.15.0](https://github.com/mollie/mollie-api-php/compare/v4.0.0...v3.15.0) - 2026-09-23

v3.15.0 brings rate-limit visibility and opt-in HTTP 429 retries to the PHP 7.4-compatible v3 line. Existing clients keep their current retry behavior unless they select the new strategy.

### Added

- `Response::header()` and `headers()` expose response headers. `Response::rateLimit()` parses Mollie's `RateLimit` and `RateLimit-Policy` headers into a `RateLimit` object with the policy, remaining requests, restore time, burst, quota, and window. Missing or malformed rate-limit headers return `null`.
- `TooManyRequestsException::getRetryAfterSeconds()` exposes `Retry-After` as a delay in seconds, accepting both integer seconds and HTTP-date values.
- `ExponentialRetryStrategy` can retry temporary network failures and HTTP 429 responses. It supports exponential backoff, optional jitter, and a maximum delay budget. A 429 whose `Retry-After` exceeds that budget is thrown rather than delayed.
- `ConditionalRetryStrategyContract` lets custom strategies decide which exceptions to retry and use the triggering exception when calculating a delay.

### Compatibility

`LinearRetryStrategy` remains the default and continues to retry temporary network failures as before. The original `RetryStrategyContract` is unchanged, so existing custom strategies require no migration. The new behavior is opt-in:

```php
use Mollie\Api\Http\ExponentialRetryStrategy;

$client->setRetryStrategy(new ExponentialRetryStrategy());

```
See the [retry guide](https://github.com/mollie/mollie-api-php/blob/v3.15.0/docs/retries.md) for configuration and custom strategy examples, or [compare v3.14.0 with v3.15.0](https://github.com/mollie/mollie-api-php/compare/v3.14.0...v3.15.0) for all changes.

## [v4.0.0](https://github.com/mollie/mollie-api-php/compare/v4.0.0-beta.3...v4.0.0) - 2026-09-23

The first stable v4 release requires PHP 8.2 or newer. See [UPGRADING.md](UPGRADING.md) for the v3 migration guide and the beta entries below for detailed changes.

### Added

- `PaymentMethod::Wero` brings the v3.14.0 payment method addition to v4. The enum remains SDK vocabulary, not an allow-list; unknown methods still arrive as strings.

### Included from the v4 betas

- String-backed enums, typed resources, readonly value objects, and inferred `MollieApiClient::send()` return types.
- `Money::of()` builders, exponential retries with `Retry-After` handling, improved validation errors, and typed fake responses.
- Profile webhook events, webhook snapshot hydration, and the v3 endpoint additions through v3.13.2.
- Request serialization preserves `0`, `"0"`, and `0.0`; the latest v3 fix is already present in v4. See the beta.2 notes for related request factory behavior.

## [v3.14.0](https://github.com/mollie/mollie-api-php/compare/v3.13.2...v3.14.0) - 2026-09-07

### What's Changed

* fix: support applicationFee on payment link creation by @Naoray in https://github.com/mollie/mollie-api-php/pull/895
* Update signature-verification.md by @fjbender in https://github.com/mollie/mollie-api-php/pull/902
* Do not drop request fields whose value is 0, "0" or 0.0 by @winklemad in https://github.com/mollie/mollie-api-php/pull/907
* fix: repair release changelog automation by @Naoray in https://github.com/mollie/mollie-api-php/pull/909
* Reconcile main with the published v3 releases by @Naoray in https://github.com/mollie/mollie-api-php/pull/912
* Add wero as supported payment method by @robindirksen1 in https://github.com/mollie/mollie-api-php/pull/924

### New Contributors

* @winklemad made their first contribution in https://github.com/mollie/mollie-api-php/pull/907
* @robindirksen1 made their first contribution in https://github.com/mollie/mollie-api-php/pull/924

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.13.0...v3.14.0

## [v4.0.0-beta.3](https://github.com/mollie/mollie-api-php/compare/v4.0.0-beta.2...v4.0.0-beta.3) - 2026-08-26

### Added

- `PaymentStatusReason` value object (`code`, `message`); `Payment::$statusReason` is now `?PaymentStatusReason` instead of an untyped `stdClass`. `->code`/`->message` reads and `json_encode()` keep working; update `instanceof stdClass` checks, any mutation (the object is readonly), and array-only consumers by calling `toArray()`.
- `PaymentMethod` cases `Billink`, `Bizum`, `Mobilepay`, `Vipps`, and `Voucher`. Existing cases are unchanged; the enum documents known SDK vocabulary, not an allow-list.
- `CapabilityStatus::Unrequested` and `Capability::isUnrequested()`.
- `Balance::$pendingAmount` (`?Money`), the amount field the Balance API returns.
- `ResourceHydratableRequest::hydrateInto()` and `::wrapInto()`. Both carry `@phpstan-self-out` and `@psalm-this-out` annotations that PHPStan honors so `send()` infers the re-targeted class or wrapper. `setHydratableResource()` is unchanged but cannot narrow the type.

### Changed

- **`PaymentMethodStatus` and `TerminalPairingCodeStatus` are string-backed enums.** Their `SCREAMING_SNAKE` constants are gone, the same migration the other value sets took in beta.1. `PaymentMethodStatus::NOT_REQUESTED` has no case: `Method::$status` is `null` for a method that was never requested. `TerminalPairingCode::$status` is typed `TerminalPairingCodeStatus|string`.
- `Method::$status` no longer has a `null` default. The API marks the field required (nullable), so an omitted field now stays uninitialized instead of reading as "not requested"; an explicit `null` still means the method was never requested.

### Fixed

- **Nullable enum unions now hydrate to enum cases.** `Enum|string|null` properties resolved as `mixed` and kept the raw API string. Affected: `Payment::$method`, `Payment::$sequenceType`, `Refund::$status`, `Mandate::$status`, `Settlement::$status`, `Profile::$status`, `CurrentProfile::$status`, `Invoice::$status`, `Subscription::$status`, and, after its enum migration, `Method::$status`. Code written against beta.2 that compares these to raw strings must compare with the case or `->value`, or use `Utility::equals()`, which accepts the case or raw value on either side. `MandateCollection::whereStatus()` accepts a `MandateStatus` case or raw string. Unknown values still arrive as strings; `null` is unchanged. `Profile::$categoryCode` (`int|string|null`) keeps the delivered scalar type.
- `Organization::$address`, `$registrationNumber`, and `$vatNumber` are nullable with a `null` default, matching the API contract. Beta.2 threw `TypeError` on `null` and `Error` on an omitted field. `Organization::$locale` stays a required, non-null `string`.
- `Capability::$statusReason` accepts `null`; `Capability::$organizationId` is nullable with a `null` default because the field is not part of the Capability response.
- `Balance` no longer fails on a conformant response: `$incomingAmount` and `$outgoingAmount` (both deprecated because they are not part of the Balance response), `$transferFrequency`, and `$transferThreshold` are nullable with a `null` default.
- Fields the API contract marks nullable or optional no longer throw `TypeError` on `null` or `Error` when omitted: `Terminal::$brand`, `$model`, `$serialNumber` (`?string`); `Terminal::$timezone`, `$locale` (`?string = null`); `Capture::$amount` (`?Money`); `PaymentLink::$profileId` (`?string`); `Webhook::$profileId` (`?string`); `Partner::$partnerType` (`?string`); `Partner::$partnerContractUpdateAvailable` (`?bool = null`); `BalanceTransaction::$deductions` (`?Money = null`); `BalanceTransaction::$mode` (`?string = null`); `Route::$releaseDate` (`?string = null`); `ConnectBalanceTransfer::$category` (`?string = null`); `SalesInvoice::$paymentTerm`, `$currency`, `$webhookUrl` (`?string = null`); and `SalesInvoice::$lines` (`?array = null`). Other `SalesInvoice` fields are unchanged pending further contract review.
- UPGRADING.md no longer prints a Types class count and now documents enum reflection, rebuilding readonly value objects, uninitialized typed properties, wrapped-request inference, and caller-side `strict_types` behavior.

### For contributors

- A test asserts every file under `src/Types/` is a backed enum except the query helpers and `Types\Method`.
- A PHPStan fixture under `tests/` asserts the inferred `send()` types on every analysis run.

## [v4.0.0-beta.2](https://github.com/mollie/mollie-api-php/compare/v4.0.0-beta.1...v4.0.0-beta.2) - 2026-08-25

### Added

- `RateLimit` value object and `Response::rateLimit()` accessor for `RateLimit` and `RateLimit-Policy` response headers.
- `middleware()->onResolved()`, a post-hydration middleware phase for transforms that need the hydrated resource or collection rather than the raw response.
- `Mollie\Api\Utils\Utility::isTrue()`, the shared boolean coercion used for API-facing scalar values such as `testmode` arriving from a query string or payload.

### Changed

- `ExponentialRetryStrategy` skips 429 retries when `Retry-After` exceeds `maxDelayMs` and adds bounded, additive jitter when honoring the header.
- `ExponentialRetryStrategy` applies the `maxDelayMs` cap before exponential full jitter, avoiding a probability spike at the cap.
- **`onResponse()` callbacks now always receive the raw `Response`**, regardless of priority. Move transforms that expect a hydrated resource or collection to `onResolved()`. See [UPGRADING.md](UPGRADING.md) section 3.7.
- **Custom endpoint maps are declared with a class constant.** Subclasses that mutated the protected static `$endpoints` property must override the protected `ENDPOINTS` constant instead. See [UPGRADING.md](UPGRADING.md) section 3.6.
- The one-shot idempotency key is now transferred to the request while it is assembled and cleared from the connector immediately, so a failed or exhausted retry can no longer leak it into a later request. Retries reuse the key already on the assembled request.
- Test mode is resolved once per request, with API-key precedence, so the value observed on the request cannot drift from the value sent to Mollie.
- Balance transaction listing propagates one effective test mode across every list, page and iterator route.
- Request factories resolve values by key presence rather than truthiness, so explicitly supplied falsy values such as `0`, `"0"` and `0.0` are no longer dropped.
- `ResourceRegistry` keeps its class and type indexes consistent, and paginated query factories build their query from one authoritative input map.

### Removed

- `Mollie\Api\Http\Middleware\ResetIdempotencyKey`. It cleared the connector key from the response phase, which never runs when a request throws. Clearing now happens during request assembly, so no replacement is needed. `setIdempotencyKey()` and `resetIdempotencyKey()` are unchanged.

### For contributors

- Formatting moved from PHP-CS-Fixer to [Laravel Pint](https://laravel.com/docs/pint). Run `composer format` to apply and `composer check:format` to verify. The rule set is unchanged, so no reformatting is required on in-flight branches.
- The code style workflow reports violations instead of committing fixes back to the branch, and CI validation reads the repository's real PHPStan and PHPUnit configuration.
- `bin/release` publishes only from an already-merged remote state and honors the operator's tag signing configuration.
- The changelog workflow derives its identity from the release event and triggers on `published`, so pre-releases are no longer skipped.

## [v3.13.2](https://github.com/mollie/mollie-api-php/compare/v3.13.1...v3.13.2) - 2026-08-24

### What's Changed

* Update signature-verification.md by @fjbender in https://github.com/mollie/mollie-api-php/pull/902
* Do not drop request fields whose value is 0, "0" or 0.0 by @winklemad in https://github.com/mollie/mollie-api-php/pull/907

### New Contributors

* @winklemad made their first contribution in https://github.com/mollie/mollie-api-php/pull/907

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.13.1...v3.13.2

## [v4.0.0-beta.1](https://github.com/mollie/mollie-api-php/compare/v3.13.0...v4.0.0-beta.1) - 2026-08-12

PHP 8.2+ modernization. See [UPGRADING.md](UPGRADING.md) for the full guide.

### Breaking changes

- **PHP 8.2+ required.** PHP 7.4, 8.0, 8.1 dropped. CI matrix is 8.2, 8.3, 8.4.
- **Type constants → string-backed enums.** The API value-set classes under `src/Types/` are now `enum ... : string` with `PascalCase` cases (`PaymentStatus::Paid`). `PaymentMethodStatus` and `TerminalPairingCodeStatus` followed after v4.0.0-beta.2; query helpers and `Types\Method` stay classes. Resource `$status`-style properties are typed `EnumName|string`. The `Mollie\Api\Traits\GetAllConstants` trait is removed with this migration — call `cases()` on the enum instead; `BusinessCategory`, `ConnectBalanceTransferCategory`, and `SubscriptionStatus` keep a static `all()` returning the raw values.
- **Resource properties typed.** Fields previously typed `\stdClass` are now concrete value objects. Property names are unchanged — `$payment->amount->value` and `->currency` still work.
- **Value objects are `readonly class`.** `Money`, `Address`, `OrderLine` etc. cannot be subclassed by non-readonly children. Prefer the new `Macroable` extension point.
- **Constructor signatures via promotion.** Named arguments unchanged; positional callers may need to reorder.
- **Typed signatures throughout.** Coercion of your arguments depends on `strict_types` in *your* files, not the SDK's; see UPGRADING.md section 3.3.
- **`Macroable` on `Money`** — undefined methods now throw `BadMethodCallException` instead of PHP's default fatal error.
- **PHPUnit + Paratest → Pest v3** in `require-dev` (consumer impact only if running SDK tests).

### Added

- Generic `@template` return type on `MollieApiClient::send()` — return type inferred from the request class. Resolves [#875](https://github.com/mollie/mollie-api-php/issues/875).
- `Money::of(string $currency)` fluent builder with `minorUnits(int $amount)` and `fromString(string $value)`. Resolves [#876](https://github.com/mollie/mollie-api-php/issues/876).
- `ExponentialRetryStrategy` with optional jitter and HTTP 429 (`Retry-After`) support.
- Typed `MockResponse` factories: `payment(...)`, `customer(...)`, `subscription(...)`, `mandate(...)`, `refund(...)`, `chargeback(...)`, `method(...)`, `paymentLink(...)`, `invoice(...)`, `capture(...)`.
- `Macroable` trait for `Money` (and other value objects) for custom factories without subclassing.
- `ValidationException` exposes per-field errors; `TooManyRequestsException` exposes `retryAfterSeconds`; `Response` exposes header access.
- 4 profile webhook event classes (`ProfileCreated`, `ProfileVerified`, `ProfileBlocked`, `ProfileDeleted`) for constants-to-class parity.

### Changed

- `ResourceHydrator` rewritten reflection-based so it can populate the new typed resource properties (value objects, enums, nested collections). Origin routing (HTTP vs. webhook snapshot) behaves exactly as in v3.13.
- Constructor promotion applied across Request and Exception classes.

### Removed

- PHP 7.4 / 8.0 / 8.1 support.
- Paratest dev dependency (Pest has `--parallel` built in).
- `Mollie\Api\Traits\GetAllConstants` trait — superseded by the enum migration. Use the enum's native `cases()`; `BusinessCategory`, `ConnectBalanceTransferCategory`, and `SubscriptionStatus` keep a static `all()` returning the raw string values.

### Fixed

- Exception messages no longer include invalid authentication tokens or request body contents, while structured exception accessors remain available.
- `docs/webhooks.md` previously stated that `$event->entity()` returns null for simple payloads. It actually throws. Updated to correctly describe reading the nullable `$event->entity` property or fetching the resource via `$event->entityId`.

## [v3.13.1](https://github.com/mollie/mollie-api-php/compare/v3.13.0...v3.13.1) - 2026-06-08

## What's Changed

* fix: support applicationFee on payment link creation by @Naoray in https://github.com/mollie/mollie-api-php/pull/895

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.13.0...v3.13.1

## [v3.13.0](https://github.com/mollie/mollie-api-php/compare/v3.12.0...v3.13.0) - 2026-06-01

## What's Changed

* feat: add terminal pairing code endpoints by @gabrielciobanu-mollie in https://github.com/mollie/mollie-api-php/pull/894
* feat: re-add payouts endpoints (restored after revert) by @Naoray in https://github.com/mollie/mollie-api-php/pull/893

## New Contributors

* @gabrielciobanu-mollie made their first contribution in https://github.com/mollie/mollie-api-php/pull/894

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.12.0...v3.13.0

## [v3.12.0](https://github.com/mollie/mollie-api-php/compare/v3.11.0...v3.12.0) - 2026-05-19

## What's Changed

* fix: add scopes query parameter to list customer mandates by @Naoray in https://github.com/mollie/mollie-api-php/pull/887
* feat: add Google Pay direct integration support by @Naoray in https://github.com/mollie/mollie-api-php/pull/888
* fix: pass settlement pagination filters by @Naoray in https://github.com/mollie/mollie-api-php/pull/889

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.11.0...v3.12.0

## [v3.11.0](https://github.com/mollie/mollie-api-php/compare/v3.10.0...v3.11.0) - 2026-05-06

### Added

- `Mollie\Api\Contracts\ResourceOrigin` marker interface describing where
  a hydrated resource came from. `Http\Response` now implements it.
- `BaseResource::getOrigin()` / `setOrigin()` accessors on every hydrated
  resource and collection. HTTP-hydrated resources set origin to the
  `Response` automatically; no migration needed for existing user code.
- `Mollie\Api\Webhooks\WebhookSnapshotOrigin` exposes the event id,
  signature, and received-at timestamp of the webhook that produced a
  hydrated resource. Accessible via `$resource->getOrigin()`.
- `Mollie\Api\Webhooks\SnapshotHydrator` feeds a webhook snapshot through
  the main `ResourceHydrator` after a one-line `json_decode(json_encode())`
  normalization so nested values arrive as stdClass (matching the HTTP
  path byte-for-byte).
- `BaseEvent::asResource(Connector)` hydrates the embedded entity into a
  fully-typed SDK resource and automatically threads the rich origin
  (event id, signature, received-at).
- `WebhookEventMapper::processPayload()` gains an optional `?string $signature` parameter that is threaded through to the resulting event
  and carried onto hydrated resources via `WebhookSnapshotOrigin`.
- `ResourceCollection::withOrigin()` factory as the origin-aware sibling
  of `withResponse()`.

### Changed

- **BC-implied:** `IsResponseAware::getResponse()` return type narrowed
  from `Response` to `?Response`. HTTP-hydrated resources continue to
  return a non-null `Response`, matching pre-refactor behavior.
  Webhook-hydrated resources return `null`. User code that chains
  `$resource->getResponse()->successful()` or similar without a null
  check will NPE on webhook-origin resources — audit your webhook
  handlers before upgrading. HTTP-only consumers see no change.
- **BC-implied:** `HasResponse::getPendingRequest()` return type
  narrowed from `PendingRequest` to `?PendingRequest`. Same rationale.
- `WebhookEntity::asResource()` gains an optional
  `?WebhookSnapshotOrigin` second parameter. Callers using the
  single-arg form (`$event->entity()->asResource($mollie)`) continue to
  work and receive a fallback origin with null signature. Mapper-driven
  flow (`$event->asResource($mollie)`) passes the rich origin
  automatically.
- Hydrating a webhook payload no longer requires a valid API key on the
  connector. Signed snapshots are self-sufficient, so a signing-secret-only
  webhook worker can read the snapshot without any key. **Follow-up calls**
  (`$payment->refunds()`, `$subscription->payments()`, etc.) still
  require an authenticator — they fire real HTTP requests.
- Follow-up methods on hydrated resources (`Payment::refunds/captures/chargebacks`,
  `Profile::chargebacks/methods/payments/refunds`, `Subscription::payments`)
  now fall back to their endpoint collection when the embedded webhook
  snapshot does not carry the corresponding `_links.{name}.href`.
  Previously these methods returned an empty collection in that case,
  which was a silent behavioural difference between HTTP-origin and
  webhook-origin resources. With this change the SDK routes through
  the connector using the resource's id (same pattern
  `PaymentLink::payments()` already used), so you get a live child
  collection on both origins. Relative `_links.{name}.href` values in
  webhook payloads are resolved against the client's base URL via
  `Url::join`, no special handling required on the caller's side.

### For contributors

- `WebhookEventMapper::createWebhookEntityFromPayload()` switched from
  `array_pop($_embedded)` to key-agnostic iteration that picks the first
  candidate carrying `id` and `resource` fields. Mollie keys the
  embedded entity under `_embedded.entity`; the new iteration resolves
  that correctly and is resilient to any future schema tweak (additional
  `_embedded` sub-blocks, renamed key) without silently breaking webhook
  handling.

### Removed

- `WebhookEntity::buildSyntheticResponse()` and its dependencies on
  `PendingRequest`, `DynamicGetRequest`, `Nyholm\Psr7\Request`, and
  `Nyholm\Psr7\Response`. Webhook hydration goes through
  `SnapshotHydrator`.
- `protected Response $response` property on the `HasResponse` trait.
  Storage is now `?ResourceOrigin $origin`; the `getResponse()` accessor
  narrows back to `?Response` for callers. Third-party subclasses that
  read `$this->response` directly must migrate to `$this->getResponse()`
  or `$this->getOrigin()`.

### Fixed

- `docs/webhooks.md` previously stated that `$event->entity()` returns
  null for simple payloads. It actually throws. Updated to correctly
  describe reading the nullable `$event->entity` property or fetching
  the resource via `$event->entityId`.

## What's Changed

* refactor(webhooks): decouple hydrated resources from the HTTP domain by @Naoray in https://github.com/mollie/mollie-api-php/pull/880
* fix(webhooks): hydrate entity locally from signed snapshot by @Naoray in https://github.com/mollie/mollie-api-php/pull/879
* fix: change customer email property type to a nullable string by @edwinvdpol in https://github.com/mollie/mollie-api-php/pull/882
* fix: make all UpdateSalesInvoiceRequest params optional by @Naoray in https://github.com/mollie/mollie-api-php/pull/885
* fix: make all UpdateSalesInvoiceRequest params optional by @fjbender in https://github.com/mollie/mollie-api-php/pull/884

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.10.0...v3.11.0

## [v3.10.0](https://github.com/mollie/mollie-api-php/compare/v3.9.0...v3.10.0) - 2026-04-15

## What's Changed

* Update documentation link for methods API by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/866
* docs: add full OAuth permission scopes list and link to official reference by @Naoray in https://github.com/mollie/mollie-api-php/pull/867
* docs: fix documentation reference url by @dionnijssen in https://github.com/mollie/mollie-api-php/pull/871
* Fix: Add missing `googlepay` type to wallet constants by @NormanAlbert91 in https://github.com/mollie/mollie-api-php/pull/869
* feat: Add BACS mandate method for UK direct debit support by @sandervanhooft in https://github.com/mollie/mollie-api-php/pull/870
* Fix PHPStan CI: replace ramsey/composer-install with plain composer install by @Naoray in https://github.com/mollie/mollie-api-php/pull/873
* Fix PHP 8+ deprecation warning in CreatePaymentRefundRequest by @Naoray in https://github.com/mollie/mollie-api-php/pull/872
* Fix onFatal callback bug + strict comparison consistency by @Naoray in https://github.com/mollie/mollie-api-php/pull/874
* feat: add lines, addresses and minimumAmount to Payment Links API by @Naoray in https://github.com/mollie/mollie-api-php/pull/877
* feat(sessions): align session endpoint with API specification by @Naoray in https://github.com/mollie/mollie-api-php/pull/858

## New Contributors

* @dionnijssen made their first contribution in https://github.com/mollie/mollie-api-php/pull/871

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.9.0...v3.10.0

## [v3.9.0](https://github.com/mollie/mollie-api-php/compare/v3.8.0...v3.9.0) - 2026-02-09

## What's Changed

* Fix: Don't call deprecated `setAccessible()` by @derrabus in https://github.com/mollie/mollie-api-php/pull/852
* Fix documented `Capability::$requirements` structure by @derrabus in https://github.com/mollie/mollie-api-php/pull/853
* feat(auth): add setToken helper for api keys by @Naoray in https://github.com/mollie/mollie-api-php/pull/859
* Chore/enhance sdk with docs and convenience methods by @Naoray in https://github.com/mollie/mollie-api-php/pull/855
* Fix/wrong payment details attribute by @Naoray in https://github.com/mollie/mollie-api-php/pull/854
* Remove incorrect null return type from ClientLink::getRedirectUrl() by @Naoray in https://github.com/mollie/mollie-api-php/pull/865
* Remove incorrect null return type from ClientLink::getRedirectUrl()   by @NormanAlbert91 in https://github.com/mollie/mollie-api-php/pull/862

## New Contributors

* @derrabus made their first contribution in https://github.com/mollie/mollie-api-php/pull/852
* @NormanAlbert91 made their first contribution in https://github.com/mollie/mollie-api-php/pull/862

**Full Changelog**: https://github.com/mollie/mollie-api-php/compare/v3.8.0...v3.9.0

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
