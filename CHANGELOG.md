# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-06-13

### Added
- `HasExpiration` — `expires_at` lifecycle with fluent helpers and query scopes
- `HasSlugs` — auto-generate URL slugs on save
- `HasSearch` — `?search=…` LIKE filter against an allowlist of columns
- `HasSorting` — `?sort=-col,col2` multi-column ordering from query parameters
- `CanIncludeRelationships` — controller-driven eager loading via `?include=` and `?with_count=`
- `HasChildren` / `HasParent` — Single-Table Inheritance with type discriminator
- `InMemory` — Sushi-style models backed by in-memory or cached SQLite
- `ExistsEloquent` / `UniqueEloquent` — Eloquent-aware validation rules with builder closures
- `StrongPassword` — pre-baked strong-password rule presets
- `ValidUlid` — validate ULID format before hitting the database
- `Actionable` — building block for action / service classes
- `HasValidation` — embeds a Laravel validator into any class
- `HasDispatcher` — lightweight event dispatcher mixin
- `PasswordBroker` + `DatabaseTokenRepository` — immutable drop-in replacement for Laravel's password reset
- `route:show` — enhanced route listing with filtering, sorting, JSON output, and middleware expansion
- PHPStan / Larastan level-8 analysis

[Unreleased]: https://github.com/deplox/laravel-support/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/deplox/laravel-support/releases/tag/v1.0.0
