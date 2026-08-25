# v4 Audit Showcase

This document records the 16 confirmed audit fixes implemented for v4, the small
structural cleanup made after the audit review, and the three line-level review
comments resolved on top of them. It explains the behavior, the invariants now
encoded in the code, the affected surface, and the evidence used to validate each
change.

## Delivery snapshot

- Branch: `feat/v4-audit-simplifications`
- Base: `a308d054cf60c18a9c2025af1e69c46d857428e1`
- Audit head: `e429a2ac980358ce0602ae4372b37540b83bc008`
- Structural cleanup head: `235d2b3cba4cbf92b6d73a60cce69e25fa00ffd6`
- Last code commit: `95662e2b0eb50a148fa0283e545dee59bf1e306e`
- Scope: 16 completed audit todos, one accepted follow-up cleanup, two of three
  resolved review comments, and the review nits raised against them
- Publication state: recorded in "Publication state" below.

## Audit at a glance

| Todo | Finding | Outcome | Commit |
| --- | --- | --- | --- |
| 3238 | R10: one-shot idempotency ownership | The connector transfers and clears the next-request key during assembly; retries retain the assembled header. | `1e8d1aa8` |
| 3239 | R12: cursor direction | Forward and backward iteration each use one consistent continuation pair. | `58bc6f97` |
| 3240 | R06: factory value resolution | Explicit values win by key presence, including falsy values and `null`. | `58bc6f97` |
| 3241 | R07: paginated factory input | Constructor data and `withQuery()` populate one authoritative query map. | `58bc6f97` |
| 3242 | R24: changelog identity | Release-event fields determine the changelog version and repository update. | `42af0bd6` |
| 3243 | R01: endpoint registration | A protected endpoint map replaces mutable static registration state. | `357895bb` |
| 3244 | R18: fake response ownership | Serialized fakes are rebuilt from authoritative transport state. | `c95ba9ac` |
| 3245 | R21: validation inputs | CI reads the real PHPStan and PHPUnit configuration files. | `d514d5eb` |
| 3246 | R11: resolved test mode | One immutable request snapshot resolves observable and outbound test mode. | `1e8d1aa8` |
| 3247 | R19: fake JSON construction | Fixtures are decoded, modified structurally, and encoded once. | `c95ba9ac` |
| 3248 | R25: formatting ownership | CI checks formatting through Composer and never writes the branch. | `d514d5eb` |
| 3249 | R23: release source of truth | Releases are published only from one verified, already-merged remote state. | `42af0bd6` |
| 3250 | R05: hydration and decoration | Hydration targets and result wrappers are separate concepts. | `6ea61783` |
| 3251 | R20: middleware phases | Raw-response and resolved-result middleware have distinct contracts. | `6ea61783` |
| 3252 | R13: registry invariants | Class and type indexes are updated atomically and remain consistent. | `8edd4715` |
| 3253 | R03: balance transaction test mode | Every list, page, and iterator route propagates one effective mode. | `e429a2ac` |

## Runtime flows

### Request lifecycle: R10, R11, and R03

```text
connector settings + request settings + final URI/payload
                         |
                         v
               assemble PendingRequest
                  /              \
                 v                v
      transfer idempotency key   resolve test mode once
      and clear connector slot   (API-key precedence)
                 |                |
                 +-------+--------+
                         v
                   outbound request
                         |
                  retry same assembly
                         |
                         v
              response / thrown exception
```

The connector owns only the next idempotency key. The assembled request owns the
header used by every retry. Test mode is resolved from the final outbound form,
so observable state cannot drift from the request sent to Mollie.

### Pagination and factories: R12, R06, and R07

```text
constructor input / withQuery()
             |
             v
  one normalized factory map
             |
   key exists? ---- no ----> fallback
       |
      yes
       v
 return exact value, including false / 0 / "" / [] / null
             |
             v
     serialize page query
             |
      +------+------+
      |             |
   forward       backward
 hasNext/next  hasPrevious/previous
```

The post-audit cleanup makes the two paginated factories reuse the same parent
normalization path instead of maintaining a mirrored constructor pipeline.

### Endpoint and registry structure: R01 and R13

```text
client subclass
     |
static::ENDPOINTS --------------> endpoint lookup
                                     |
                                     v
                              instantiate endpoint

resource registration
     |
normalize names -> preflight every collision -> update both indexes atomically
                                              /                     \
                                             v                       v
                                       class index              type index
```

Both areas now express registration as data. There is no constructor-time
mutation for endpoints and no partially updated resource registry.

### Fake response construction: R18 and R19

```text
owned JSON fixture
      |
decode with JSON_THROW_ON_ERROR
      |
insert caller values into decoded keys and values
      |
encode once
      |
authoritative body + status + resource key
      |
versioned serialized fake
```

Caller-supplied values never become replacement syntax, and unserialization
cannot silently construct a fake from malformed or ambiguous state.

### Response processing: R05 and R20

```text
SDK Response
     |
onResponse middleware: Response -> Response | void
     |
hydrate canonical target: BaseResource | ResourceCollection
     |
apply optional wrapper exactly once
     |
onResolved middleware: mixed -> mixed | void
     |
caller result
```

Raw transport mutation, hydration, decoration, and resolved-result mutation are
now separate phases with explicit ownership and invalid return values rejected at
the phase boundary.

### Automation and release: R21, R25, R24, and R23

```text
pull request / push                     published GitHub release event
        |                                           |
        v                                           v
read real configs                         derive version from event tag
        |                                           |
PHPStan + PHPUnit + format check          update canonical changelog
        |                                           |
no branch-writing formatter               commit on default branch

manual release command
        |
verify clean default branch == fetched origin/default == CLIENT_VERSION
        |
classify tag/release state
        |
no-op | recover tag-only state | push one annotated tag and create release
```

Checks observe committed inputs. Release automation does not invent a second
source branch, mutate the version, or publish from unmerged local state.

## Detailed fixes

### 1. Todo 3238, R10: transfer the one-shot idempotency key during assembly

**Problem.** The explicit next-request key remained on the connector when a
request failed before the old reset point. A later unrelated request could reuse
it accidentally.

**Fix and invariant.** Request assembly now transfers the key from the connector
and immediately clears the connector slot. Mutating requests store it in their
assembled header, so all retries reuse the exact key. A GET consumes the one-shot
key without sending an ignored header. The public reset API remains available.

**Files.** `src/Http/Middleware/ApplyIdempotencyKey.php`,
`src/Http/PendingRequest.php`, `src/Traits/HandlesIdempotency.php`, and focused
client/retry tests.

**Evidence.** 32 focused tests with 71 assertions. Mollie's
[idempotency reference](https://docs.mollie.com/reference/api-idempotency)
requires retries to use the same key and documents idempotency for POST requests.

### 2. Todo 3239, R12: use one direction for cursor continuation and page fetch

**Problem.** Iterator continuation checks and page fetching could select
different directions, producing fragile traversal behavior.

**Fix and invariant.** `getAutoIterator()` chooses one pair once: forward uses
`hasNext()` with `next()`, backward uses `hasPrevious()` with `previous()`.
Laziness, page order, and the origin page are preserved.

**Files.** `src/Resources/CursorCollection.php` and cursor/payment iterator tests.

**Evidence.** 11 tests with 20 assertions. The pairing follows Mollie's
[cursor pagination reference](https://docs.mollie.com/reference/pagination).

### 3. Todo 3240, R06: resolve factory values by key presence

**Problem.** Truthiness-based lookup discarded valid explicit values such as
`false`, `0`, `"0"`, an empty string, an empty array, and `null`.

**Fix and invariant.** `Factory::get()` uses ordered key presence. The first
present key wins exactly; fallback is used only when no candidate exists. Dot
paths and public signatures remain unchanged.

**Files.** `src/Factories/Factory.php` and `tests/Factories/FactoryTest.php`.

**Evidence.** Focused factory tests: 10/10; full factory tests: 174 tests with
275 assertions; array utility tests: 13 tests with 25 assertions.

### 4. Todo 3241, R07: give paginated query factories one input map

**Problem.** Constructor values and explicit query values followed separate data
paths, forcing callers and endpoint code to reason about two competing shapes.

**Fix and invariant.** Constructor data seeds the existing query map, and
`withQuery()` replaces that same map. Arrayable input, named arguments, and falsy
values are preserved. Payment-link and settlement paths serialize `from`,
`limit`, and `sort` from the same source.

**Files.** Both paginated query factories, the payment-link payment endpoint,
and factory/endpoint regression tests.

**Evidence.** 14 focused tests with 93 assertions; factory suite 184/300;
endpoint suites 23/256.

### 5. Todo 3242, R24: derive changelog identity from release event fields

**Problem.** The workflow could derive release identity from repository text
that did not reliably match the release event.

**Fix and invariant.** The workflow uses `github.event.release.tag_name`, checks
out the default branch, preserves the approved linked Unreleased section, and
lets checked-in notes remain authoritative. It requests only the write permission
needed for the changelog commit.

**Files.** `.github/workflows/update-changelog.yml` and `CHANGELOG.md`.

**Evidence.** YAML parsing, a local release-event fixture, a source-faithful
updater run, a no-op rerun, and release-note preservation all passed.

**Operational caveat.** Direct Actions commits require branch protection to
allow the configured workflow identity.

### 6. Todo 3243, R01: make endpoint registration structural

**Problem.** Endpoint registration depended on mutable static state initialized
through constructor behavior.

**Fix and invariant.** A protected, overridable `ENDPOINTS` constant is read via
`static::ENDPOINTS`. The 43 public mappings, lazy construction, and unknown
endpoint exception remain compatible. Subclasses extend the map structurally.

**Files.** `src/Traits/HasEndpoints.php`, client tests, and the v4 upgrading guide.

**Evidence.** 45 tests with 203 assertions; independent review probes 17/149;
all 43 mappings verified.

### 7. Todo 3244, R18: serialize fake responses from authoritative state

**Problem.** A fake could be reconstructed from derived or ambiguous state
instead of the body, status, and resource identity used by the transport.

**Fix and invariant.** Versioned serialization stores the authoritative body,
status, and resource key. The exact legacy three-key shape remains readable.
Unknown versions, malformed shapes, wrong types, and unrecoverable legacy status
strings fail before state is assigned.

**Files.** `src/Fake/MockResponse.php` and its regression tests.

**Evidence.** Fake suite: 55 tests with 137 assertions; reviewer probes: 17/37.

### 8. Todo 3245, R21: make validation workflows observe their real inputs

**Problem.** CI referenced distribution filenames while development used the
committed project configuration, allowing local and automated checks to diverge.

**Fix and invariant.** Workflows now read `phpstan.neon` and `phpunit.xml`.
Trigger names, matrices, and validation steps remain otherwise unchanged.

**Files.** `.github/workflows/phpstan.yml` and `.github/workflows/tests.yml`.

**Evidence.** Ruby YAML parsing, exact-transform checks, and offline event/path
matrix checks passed. `actionlint` was unavailable locally.

### 9. Todo 3246, R11: centralize resolved test mode with API-key precedence

**Problem.** Test mode was recomputed from mutable connector/request state after
the outbound request had been shaped. Continuation URLs could also expose a mode
different from the one actually sent.

**Fix and invariant.** `PendingRequest` snapshots the effective mode. Test API
keys force `true`; live API keys force `false`. Other authentication reads the
connector, request, and final merged URI/payload with safe boolean parsing.
`HandleTestmode` shapes outbound parameters from that snapshot, so later mutation
cannot cause drift.

**Files.** `src/Http/PendingRequest.php`,
`src/Http/PendingRequest/HandleTestmode.php`, and pending-request/continuation tests.

**Evidence.** Combined R10/R11 coverage: 53 tests with 117 assertions;
continuation coverage: 32/130. Mollie's
[testing reference](https://docs.mollie.com/reference/testing) documents test API
keys and the `testmode` parameter for advanced and app-token flows.

### 10. Todo 3247, R19: build fake JSON through decoded structures

**Problem.** Text replacement in JSON fixtures allowed caller values to interact
with placeholder syntax and risked invalid encoding.

**Fix and invariant.** Owned fixtures decode with `JSON_THROW_ON_ERROR`, caller
values are inserted into decoded keys and values, and the result is encoded once.
Token replacement runs once over fixture-owned structures. Placeholder-shaped
caller IDs remain literal, while direct caller JSON retains its exact bytes.

**Files.** `src/Fake/ErrorResponseBuilder.php`, `src/Fake/MockEvent.php`,
`src/Fake/MockResponse.php`, and their tests.

**Evidence.** 105 tests with 304 assertions plus independent reviewer probes.

### 11. Todo 3248, R25: make formatting CI a check, not a branch writer

**Problem.** Formatting automation owned branch mutation and depended on Docker,
making a validation job capable of rewriting source.

**Fix and invariant.** `format` and `check:format` share one pinned formatter and
one project configuration. CI runs `check:format` on PHP 8.4 with read-only
contents permission. It has no Docker, auto-commit, push, or cache action.

The formatter itself was later swapped for Laravel Pint in response to review
feedback; see "Review feedback resolutions" below. The CI ownership invariant
established here is unchanged by that swap.

**Files.** `composer.json` and `.github/workflows/fix-codestyle.yml`.

**Evidence.** Composer validation, YAML parsing, a fresh dependency solve, and
clean/dirty fixture checks passed. No root lockfile or `vendor` change was added.

### 12. Todo 3249, R23: publish from one already-merged remote state

**Problem.** The release script could combine local, remote, tag, and GitHub
states without one strict source of truth or deterministic recovery behavior.

**Fix and invariant.** Release requires a clean symbolic default branch whose
HEAD equals fetched `origin/<default>` and whose version equals `CLIENT_VERSION`.
The state table is explicit: a complete release is a no-op; an annotated tag
without a release is recovered; no tag/release creates and pushes one annotated
tag before creating the release; malformed or contradictory states abort.

GraphQL absence is accepted only when the repository object is present and
`release` is explicitly `null`. The script never mutates a version, pushes a
branch, force-pushes, deletes a tag, sleeps, or relies on implicit tag creation.

**Files.** `bin/release`.

**Evidence.** `bash -n`, ShellCheck, diff checks, and a 15-case final fixture/stub
matrix passed. An earlier 35-case matrix found and drove the GraphQL classifier
hardening. No real tag or release was created.

### 13. Todo 3250, R05: separate hydration target from wrapper decoration

**Problem.** One setting represented both the type to hydrate and the wrapper to
apply, creating invalid intermediate states and implicit request mutation.

**Fix and invariant.** Canonical hydration targets are `BaseResource` and
`ResourceCollection`; direct `BaseCollection` targets are rejected. Wrapper
decoration is stored separately and applied once to the resolved resource,
collection, lazy collection, or raw response. The legacy getter remains
wrapper-first, and reset clears only wrapper decoration.

**Files.** `src/Http/Requests/ResourceHydratableRequest.php`,
`src/Resources/ResourceResolver.php`, and focused hydration tests.

**Evidence.** 53 tests with 135 assertions.

### 14. Todo 3251, R20: split raw-response and resolved-result middleware

**Problem.** One middleware phase handled both transport responses and hydrated
results, weakening type contracts and making order-sensitive behavior hard to
reason about.

**Fix and invariant.** `onResponse` receives an SDK `Response`: `null` preserves
it, another `Response` replaces it, and other values throw. Hydration happens at
one internal boundary. `onResolved` then accepts the hydrated mixed value:
`null` preserves it and a non-null value replaces it. Each phase has its own
priority and merge order. `GetEnabledMethodsRequest` moved its null-status filter
to the resolved phase.

**Files.** Response middleware contracts, middleware traits, hydration/request
pipeline classes, enabled-method request code, tests, and `UPGRADING.md`.

**Evidence.** 89 tests with 222 assertions.

### 15. Todo 3252, R13: make ResourceRegistry indexes consistent

**Problem.** Re-registration and collision handling could leave the class and
type indexes out of sync or preserve stale names.

**Fix and invariant.** Registration derives and normalizes names, preflights
every collision, removes the previous names owned by the same class, and then
updates both indexes atomically. Re-registration replaces old names rather than
creating aliases. Identical singular/plural names for one class remain valid.

**Files.** `src/Resources/ResourceRegistry.php` and its tests.

**Evidence.** 46 tests with 192 assertions, including atomic collision and
re-registration cases.

### 16. Todo 3253, R03: align balance-transaction test-mode behavior

**Problem.** Balance transaction list, page, and iterator entry points did not
share one test-mode contract, especially when pagination queries or continuation
links supplied the mode.

**Fix and invariant.** Every path forwards one effective mode. Query `testmode`
takes precedence over the argument and is removed from pagination input before
serialization. The nullable `iteratorForPrimary()` compatibility shim maps
`null` to `false`. R11 ensures continuation links preserve matching observable
and outbound mode.

**Files.** `src/EndpointCollection/BalanceTransactionEndpointCollection.php`,
its request class, endpoint/request tests, and the pending-request integration
test.

**Evidence.** 35 tests with 134 assertions. The exact
[List balance transactions operation](https://docs.mollie.com/reference/list-balance-transactions)
documents `/v2/balances/{balanceId}/transactions`, the `primary` alias,
pagination, and boolean `testmode` for advanced/OAuth access. This operation page
is the authority for this endpoint despite the broader testing guide's warning
that most business operations are unavailable with organization access tokens.

## Accepted structural cleanup

**Verdict: implement. Opportunity: one canonical factory normalization path.**

The audit exposed a small mirrored-state risk in the paginated factory
constructors. Both factories now let their existing `Factory` parent normalize
input and seed the query from `$this->get()`. This removes duplicated shape
assumptions without changing the public inheritance hierarchy or request factory
contracts.

- Commit: `235d2b3cba4cbf92b6d73a60cce69e25fa00ffd6`
- Files: `src/Factories/PaginatedQueryFactory.php` and
  `src/Factories/SortablePaginatedQueryFactory.php`
- Validation: full Pest suite, PHPStan, and the exact formatting check passed

No larger state machine or command model was introduced. The remaining flows are
linear and local; a broader abstraction would add indirection without removing
additional invalid states.

## Commit ledger

1. `1e8d1aa8c00325be044965f03473b2d2e6b754d1` — Fix request idempotency and test-mode state
2. `58bc6f97673160499b6f6fddfe2ade608aafc638` — Simplify pagination and query factories
3. `357895bb84c8efbaf9a7d9c9197644748ea122e4` — Make client endpoint registration structural
4. `c95ba9ac2d8a2b0e0f1b1481c8b658c18802b7bc` — Build fake responses from transport state
5. `d514d5ebd12b732d5c30d6a450e28f13dd56fa21` — Make CI checks observe their inputs
6. `42af0bd629815daac89afee0791245b33dd8cdf4` — Publish releases from canonical remote state
7. `6ea61783f3783e08768c3c6da7cab5fc249c1b5b` — Separate hydration and response middleware phases
8. `8edd47151f3df9243218ed3917d7457228006335` — Keep resource registry indexes consistent
9. `e429a2ac980358ce0602ae4372b37540b83bc008` — Align balance transaction test-mode behavior
10. `235d2b3cba4cbf92b6d73a60cce69e25fa00ffd6` — Reuse canonical factory input for pagination
11. `0a0e72da3005a891f7486e64c41fb4aeb520cb3d` — Extract shared boolean parsing utility
12. `ddff798b8517bd3ecf7181b16d19a2c11b39d6c9` — Remove obsolete idempotency response middleware
13. `e1b28d6e8cd0aba95823f25a8f2a3fe90f54bf93` — Replace PHP-CS-Fixer with Laravel Pint
14. `95662e2b0eb50a148fa0283e545dee59bf1e306e` — Let the release script honour tag signing configuration

This document is added by the commit that follows them.

## Review feedback resolutions

Three line-level review comments were raised against the audit branch. Two are
resolved; one is deliberately left open pending a maintainer decision.

### Comment 1 — changelog release identity (UNRESOLVED, policy-gated)

> On `CHANGELOG.md`: "we already released a v4.0.0-beta.x version... so
> Unreleased is misleading"

**Status: not actioned. No changelog or workflow change was made.**

The complaint is substantively correct: everything under `## [Unreleased]` except
three bullets shipped in `v4.0.0-beta.1` (published 2026-08-12, `isPrerelease:
true`). But the fix is not derivable from repository evidence:

- The heading's *form* already matches convention. `origin/main:CHANGELOG.md`
  carries the identical `## [Unreleased](.../compare/<tag>...HEAD)` shape, which
  is what `stefanzweifel/changelog-updater-action` both emits and consumes.
- At the beta tag itself the heading read `## [4.0.0] - UNRELEASED`, so the
  maintainers treated the whole v4 block as one accumulating section.
- `update-changelog.yml` fires on `release: types: [released]`, which GitHub does
  not emit for pre-releases. No workflow run exists for `v4.0.0-beta.1`.
- `v4.0.0-beta.1` is the only pre-release in repository history, so there is no
  precedent for how a beta appears in `CHANGELOG.md`.

Resolving it means choosing a release policy — whether a published pre-release is
a changelog-visible release with its own `## [tag] - date` section (implying the
workflow should also trigger on `prereleased`), or a preview cut with all v4
notes accumulating until GA. Per the review instruction not to invent version or
release semantics, this was left unresolved rather than guessed. Full evidence:
`solo://proj/16/scratchpad/decision-required-v4--8352`.

### Comment 2 — replace PHP-CS-Fixer with Laravel Pint (RESOLVED)

> On `composer.json`: "I'd rather use laravel/pint"

`friendsofphp/php-cs-fixer` is removed and `laravel/pint` added at `1.30.4`.
`pint.json` reproduces the deleted `.php-cs-fixer.dist.php` exactly — preset
`empty` with `@PSR2` plus the same thirteen explicit rules over the same paths.
Running Pint across the tree rewrites zero files, which is the evidence that the
two configurations agree.

The version is pinned, not ranged: Pint `1.30.5` requires PHP `^8.3`, so `^1.29`
would resolve differently on the PHP 8.4 CI runner than on this package's PHP 8.2
floor. `composer format` runs `pint`; `composer check:format` runs `pint --test`.
`fix-codestyle.yml` keeps the read-only permissions and check-only shape from
R25, stays on Composer rather than Docker, and never writes to the branch. No
`vendor/` and no newly ignored lockfile were added.

### Comment 3 — extract boolean parsing into a utility (RESOLVED)

> On `src/Http/PendingRequest.php`: "should be a Utility function"

`PendingRequest::isTrue()` moved to `Mollie\Api\Utils\Utility::isTrue()`. A
repository search found no pre-existing scalar boolean parser; `Utility` was
chosen because it already owns the neighbouring `extractBool()` helper and has a
matching test file, so the public surface grows by exactly one static method.

Behavior is byte-identical: non-scalars short-circuit to `false`, and `filter_var`
runs with `FILTER_NULL_ON_FAILURE` so unrecognised scalars resolve to `false`
rather than through PHP's looser truthiness. Test-mode precedence and request
shaping are untouched. Coverage spans booleans, integers, floats, numeric
strings, case variants, the empty string, invalid strings, `null`, arrays,
objects, stringables, resources, and closures.

## Idempotency middleware removal

`src/Http/Middleware/ResetIdempotencyKey.php` is deleted with no replacement.

It was a `ResponseMiddleware` that cleared the connector's one-shot key from the
response phase — a phase that never runs when a request throws, so a network
error or exhausted retry left the key set and it leaked into the next request.

`ApplyIdempotencyKey` now reads and clears the key at the top of request
assembly, ahead of the mutating/non-mutating branch, so the connector is cleared
even for GET requests and even when the send later fails. `SendsRequests::send()`
builds the `PendingRequest` once outside the retry loop, so every retry reuses the
same assembled header. `setIdempotencyKey()` and the explicit
`resetIdempotencyKey()` API are unchanged and still public.

Verified before accepting the deletion: no repository code referenced the class;
mutating requests retain the transferred header; GET requests consume the key
without sending it; retries reuse the assembled key; and failure or retry
exhaustion cannot leak it forward.

## Reviewed follow-ups

A fresh reviewer audited the whole branch diff before publication and returned
NITS with zero blockers. Two of its findings were acted on:

- **`bin/release` no longer disables tag signing.** The rewritten release flow
  had created its annotated tag with `git tag --annotate --no-sign`. That flag
  overrides the operator's `tag.gpgSign` setting, so a maintainer configured for
  signing still produced unsigned release tags with no indication signing had
  been skipped. The previous script used `git tag -a`, which honoured the
  configuration; the flag is dropped to restore that. Tag classification is
  unaffected — `classify_local_tag` and `classify_remote_tag` decide
  annotated-ness from the object type and the peeled ref, and a signed annotated
  tag is still a tag object peeling to the same commit.
- **The formatting workflow is renamed** from "Fix Code Style" to "Check Code
  Style", so the workflow list stops advertising a mutation it no longer
  performs.
- **The boolean data provider now pins whitespace handling.** `filter_var` trims,
  so `' 1'` and `'true '` resolve to `true`. The helper reads values straight off
  a query string, and without those cases a rewrite that dropped the trim would
  have passed the suite unnoticed.

## Compatibility and migration notes

- Existing public signatures were preserved unless the approved v4 middleware
  contract explicitly required a phase distinction.
- `ResponseMiddleware` is now the raw `Response | void` phase; use resolved
  middleware for post-hydration transformations. See `UPGRADING.md`.
- The endpoint map remains subclass-extensible through a protected constant.
- Legacy three-field fake serialization remains readable, while ambiguous legacy
  states now fail explicitly.
- `iteratorForPrimary(null)` remains accepted and resolves to `false`.
- The release workflow assumes its identity may commit to the protected default
  branch; repository policy must allow that operation.
- `Utility::isTrue()` is new public API; it is additive and no existing signature
  changed. `PendingRequest::isTrue()` was private, so its removal is not a
  breaking change.
- `Mollie\Api\Http\Middleware\ResetIdempotencyKey` is removed. It was internal
  middleware and nothing in the repository referenced it, but any integrator who
  registered it manually should drop the registration — clearing is now handled
  during request assembly. `setIdempotencyKey()` / `resetIdempotencyKey()` are
  unchanged.
- Contributors must run `composer format` (Laravel Pint) instead of PHP-CS-Fixer.
  Formatting output is unchanged, so no reformatting commit is required on
  in-flight branches. `friendsofphp/php-cs-fixer` can be dropped from local
  tooling and `.php-cs-fixer.dist.php` no longer exists.

## Final verification

| Check | Result |
| --- | --- |
| Full Pest suite | 1,447 tests, 4,349 assertions passed |
| PHPStan | 817 files analyzed, no errors |
| Focused idempotency tests | 36 tests, 83 assertions passed |
| Focused test-mode / boolean tests | 74 tests, 103 assertions passed |
| Laravel Pint 1.30.4 | `composer check:format` passed; zero source or test rewrites |
| Pint check-does-not-write proof | A deliberately mis-formatted fixture made `check:format` exit 1 with the file's SHA-256 unchanged; `composer format` then restored the original SHA-256 |
| Composer validation | `./composer.json is valid` |
| Fresh dependency resolution | Clean-room solve on platform PHP 8.2.0 resolved `laravel/pint` 1.30.4; 1.30.5 requires PHP ^8.3 and is correctly excluded |
| Generated currency code | 29 currencies regenerated with no diff |
| Workflow YAML | All five workflows parsed successfully |
| Release script syntax | `bash -n bin/release` passed after the signing change |
| Release script lint | ShellCheck passed with no findings |
| Independent pre-publication review | Whole-branch review by a fresh reviewer: NITS, 0 blockers |
| Whitespace | `git diff --check` clean across the branch |
| Stale PHP-CS-Fixer references | None remain outside this document's historical notes |
| Stale `ResetIdempotencyKey` references | None remain outside this document's notes |
| Unrelated user file | `tools/weekly-mollie-changelog.sh` was never staged or committed and never entered git history. It has since been relocated out of the repository at the maintainer's request. |
| Release operations | None. No tag, GitHub release, deployment, remote branch rewrite, or unintended push occurred |

Not run, because the review item they belong to is unresolved: changelog-updater
event fixtures and the idempotent-rerun check. Those are deliverables of Comment
1, which is policy-gated (see above). The repository has no committed
changelog-updater or release-script fixture suite; the release-script checks
above are `bash -n` and ShellCheck against `bin/release`.

## Publication state

- Branch: `feat/v4-audit-simplifications`, pushed to `origin`.
- Base: `feat/v4-main-merge`.
- One pull request targets that base. No other branch or remote ref was created.
- No tag was created, no GitHub release was published, no deployment ran, and no
  remote branch was rewritten or force-pushed at any point. The tag list is
  unchanged from before this work.

## Source authority

- Canonical audit: `solo://proj/16/scratchpad/audit-materially-use--7705`, revision 24
- Todo conversion report: `solo://proj/16/scratchpad/spawn-5966-status--7837`
- API idempotency: <https://docs.mollie.com/reference/api-idempotency>
- Cursor pagination: <https://docs.mollie.com/reference/pagination>
- Test mode: <https://docs.mollie.com/reference/testing>
- Balance transaction operation: <https://docs.mollie.com/reference/list-balance-transactions>

The API-facing changes in this audit were accepted only where the exact Mollie
operation reference or a compatible public contract supported them. Internal
refactors were kept behavior-preserving and covered by focused regressions.
