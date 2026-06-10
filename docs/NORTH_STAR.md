# Project North Star — mollie/mollie-api-php

**Owner:** Mollie B.V.
**Maintainer of record:** see `composer.json` authors + active reviewers
**Last updated:** 2026-05-21

## Mission

Be the official, idiomatic PHP SDK for the Mollie REST API — faithful to the upstream contract, safe to upgrade, pleasant to read in a Laravel/Symfony codebase.

## Target users / adopters

- PHP backend developers integrating Mollie payments into a server-rendered or API-first product (Laravel, Symfony, Slim, plain PHP).
- Mollie Connect partners on OAuth flows.
- Internal teams at adopters (`laravel-mollie`, `laravel-cashier-mollie`) who depend on this SDK as a transitive dependency.

NOT for: Mollie's own dashboard frontend, mobile SDKs (Swift/Kotlin/Flutter), generic multi-PSP abstraction layers, browser-side checkout components.

## Non-goals

1. **Not a payment-processing engine.** Card-data handling, PCI scope, fraud rules all live server-side at Mollie. SDK never holds raw card data.
2. **Not a multi-PSP abstraction.** Will not normalize Mollie's API to look like Stripe / Adyen. Mollie's API shape leaks through deliberately.
3. **Not auto-generated from OpenAPI.** Codegen drift would erode type quality and convenience methods. Handwritten + surgically maintained.
4. **Not a UI / checkout component library.** No HTML rendering, no JS components, no payment widgets.
5. **Not a Mollie API documentation surface.** Docs at docs.mollie.com are canonical; this SDK references them, never duplicates them.

## Hard constraints

- **PHP 8.2+** on v4. v3 maintenance branch supports 7.4–8.4.
- **PSR-7/17/18** HTTP message + factory + client interfaces. Pluggable adapter (Guzzle, cURL, PSR-18).
- **BSD-2-Clause** license. Contributions land under same.
- **SemVer**: backward source-compat within a major. Breaking changes require a new major + UPGRADING.md guide + rector codemod where mechanical.
- **Composer + packagist** are the canonical distribution.
- **CI matrix matches supported PHP versions** — current: 8.2, 8.3, 8.4 on v4.
- **Test runner: Pest v3** (PHPUnit retained for legacy assertions during v4 migration).

## Decision principles

1. **Mirror Mollie's REST API faithfully.** Why: SDK is a thin convenience layer; divergence creates "the docs say X but the SDK does Y" support pain. Beats: clever DX abstractions, multi-PSP normalization.
2. **Type safety + IDE discoverability > brevity.** Why: developers spend more time reading SDK code than writing it; auto-complete is the discovery surface. Beats: terse one-liners, magic.
3. **Honest semantics > forced abstractions.** Why: a fake Response on webhook origin or a synthetic value breaks consumer trust silently. Nullable types are the truthful shape; document them, don't hide them. Beats: maintaining non-nullable signatures at the cost of lying.
4. **Backwards compatibility within a major version is sacred.** Why: SDK is load-bearing for production payments. Surprise breaks = revenue loss for adopters = trust loss for Mollie. Beats: cleanup, refactors, internal code hygiene.
5. **Two-tier API surface (modern Request + legacy EndpointCollection) coexist during v4.** Why: existing v3 users have `$client->payments->create(...)` patterns deeply baked. Force-migration on a major is too much churn for one release. Beats: codebase purity.
6. **Test as consumers test.** Why: mock-only unit tests miss the integration shape that breaks in production. `MockMollieClient` + fixture replay is the canonical pattern. Beats: 100% line coverage, micro-unit assertions.
7. **Defer features over breaking existing adopters.** Why: trust costs more to rebuild than features cost to ship later. Beats: shipping velocity.

## Success signals

- Packagist daily downloads stable or growing in the 90 days after a major release.
- No P0 (production payment failures) issues unresolved for >48h.
- Major Mollie-integration tutorials (Laravel docs, Symfony cookbook) reference current major idioms without workarounds.
- Issue churn dominated by feature requests, not "v4 broke X".
- Adopters (`laravel-mollie`, `laravel-cashier-mollie`) ship their own upgrades within ~30 days of a SDK major.

## Anti-signals

- Adopter reports v4 broke production payment capture, refund, or webhook handling.
- Open-issue queue grows faster than it closes for >3 weeks.
- Internal Mollie product owners stop reviewing PRs (signal: upstream API drifted and we missed it).
- SDK behavior drifts from docs.mollie.com (signal: API changed, SDK didn't follow).
- "Just use raw HTTP / curl" appears in adopter forums as a workaround.
