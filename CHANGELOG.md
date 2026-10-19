# Changelog

All notable changes to `laravel-url-ai-transformer` will be documented in this file.

## 2.1.0 - 2026-10-19

### What changed

- Add support for Laravel AI 1.2.3 and later 1.x releases while retaining Laravel AI 0.11 compatibility.
- Verify plain text, structured JSON, and LD+JSON transformations through Laravel AI’s HTTP gateway.
- Document migration considerations for applications choosing to upgrade Laravel AI to 1.x.
- Validate Composer metadata in CI and provide reproducible orb setup.

The test matrix uses PHP 8.4 and 8.5, Laravel 13, Pest 5.3, and PHPUnit 13. These tooling updates were already included in 2.0.1.

Applications may keep their existing Laravel AI 0.11 constraint. Before upgrading Laravel AI to 1.x, review UPGRADE.md, especially if using remembered conversations, custom middleware, MCP, Bedrock, or response usage data.

**Validation:** All PR checks passed. Each local PHP/dependency combination passed 99 tests and 222 assertions.

**Full Changelog**: https://github.com/spatie/laravel-url-ai-transformer/compare/2.0.1...2.1.0

## 2.0.1 - 2026-10-19

### Fixes

- Strip large inline script and style blocks without exhausting the PCRE backtrack limit, so page text rather than JavaScript reaches the AI.
- Recognize whitespace before the closing bracket in script and style end tags.
- Expand regression coverage for large closed/unclosed blocks, mixed-case tags, and similarly named elements.

### Requirements and tooling

- Requires PHP 8.4+ and Laravel 13. Laravel 12 applications can continue using 2.0.0.
- Update development tooling to Pest 5.3 and PHPUnit 13, with PHP 8.4/8.5 CI coverage for lowest and latest dependencies.

All four test matrix jobs and code-style checks passed on the PR and merged commit.

Thanks @HawkForge for the fix and @hanzala-afzal-dev for the detailed report.

Full changes: https://github.com/spatie/laravel-url-ai-transformer/pull/37

## 2.0.0 - 2026-07-13

Replaces Prism with the official Laravel AI package. Requires Laravel 12+. See the upgrade guide for the migration path.

## 1.3.1 - 2026-06-03

### What's Changed

* Bump dependabot/fetch-metadata from 2.5.0 to 3.0.0 by @dependabot[bot] in https://github.com/spatie/laravel-url-ai-transformer/pull/24
* Bump dependabot/fetch-metadata from 3.0.0 to 3.1.0 by @dependabot[bot] in https://github.com/spatie/laravel-url-ai-transformer/pull/25
* fix: use Prism facade instead of class for static calls in LdJsonTransformer by @Nelwhix in https://github.com/spatie/laravel-url-ai-transformer/pull/26

### New Contributors

* @Nelwhix made their first contribution in https://github.com/spatie/laravel-url-ai-transformer/pull/26

**Full Changelog**: https://github.com/spatie/laravel-url-ai-transformer/compare/1.3.0...1.3.1

## 1.3.0 - 2026-03-27

### What's Changed

* Update prism-php/prism requirement from ^0.99.0 to ^0.100.1

## 1.1.1 - 2025-10-06

### What's Changed

* Update prism-php/prism requirement from ^0.89.0 to ^0.90.0 by @dependabot[bot] in https://github.com/spatie/laravel-url-ai-transformer/pull/9

**Full Changelog**: https://github.com/spatie/laravel-url-ai-transformer/compare/1.1.0...1.1.1

## 1.1.0 - 2025-09-23

### What's Changed

* Update prism-php/prism requirement from ^0.86.0 to ^0.87.0 by @dependabot[bot] in https://github.com/spatie/laravel-url-ai-transformer/pull/5
* Update prism-php/prism requirement from ^0.87.0 to ^0.88.1 by @dependabot[bot] in https://github.com/spatie/laravel-url-ai-transformer/pull/6
* Update issue template by @AlexVanderbist in https://github.com/spatie/laravel-url-ai-transformer/pull/7
* Update prism-php/prism requirement from ^0.88.1 to ^0.89.0 by @dependabot[bot] in https://github.com/spatie/laravel-url-ai-transformer/pull/8
* Update introduction.md by @WatheqAlshowaiter in https://github.com/spatie/laravel-url-ai-transformer/pull/2
* fix: replace `new static()` with `new self()` in InvalidConfig by @Ayoub-Mabrouk in https://github.com/spatie/laravel-url-ai-transformer/pull/4

### New Contributors

* @AlexVanderbist made their first contribution in https://github.com/spatie/laravel-url-ai-transformer/pull/7
* @WatheqAlshowaiter made their first contribution in https://github.com/spatie/laravel-url-ai-transformer/pull/2
* @Ayoub-Mabrouk made their first contribution in https://github.com/spatie/laravel-url-ai-transformer/pull/4

**Full Changelog**: https://github.com/spatie/laravel-url-ai-transformer/compare/1.0.1...1.1.0

## 1.0.1 - 2025-08-26

### What's Changed

* Update prism-php/prism requirement from ^0.85.0 to ^0.86.0 by @dependabot[bot] in https://github.com/spatie/laravel-url-ai-transformer/pull/1

### New Contributors

* @dependabot[bot] made their first contribution in https://github.com/spatie/laravel-url-ai-transformer/pull/1

**Full Changelog**: https://github.com/spatie/laravel-url-ai-transformer/compare/1.0.0...1.0.1

## 1.0.0 - 2025-08-24

**Full Changelog**: https://github.com/spatie/laravel-url-ai-transformer/compare/0.0.1...1.0.0

## 0.0.1 - 2025-08-24

**Full Changelog**: https://github.com/spatie/laravel-url-ai-transformer/commits/0.0.1
