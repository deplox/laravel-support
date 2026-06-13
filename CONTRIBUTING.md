# Contributing

Thanks for considering a contribution to `deplox/laravel-support`.

## Requirements

- PHP 8.4+
- Composer

## Setup

```bash
git clone https://github.com/deplox/laravel-support.git
cd laravel-support
composer install
```

## Running the test suite

```bash
composer test          # Pest
composer stan          # PHPStan level 8
```

Both must pass before a pull request is merged.

## Workflow

1. Fork the repository and create a branch from `main`.
2. Write or update tests for any behaviour you change.
3. Ensure `composer test` and `composer stan` pass.
4. Open a pull request with a clear description of the change and why it's needed.

## Code style

- `declare(strict_types=1)` in every file.
- Traits live in the namespace that matches their domain (`Database\Eloquent\Concerns`, `Concerns`, etc.).
- Prefer contracts/interfaces over concrete classes in type hints.
- No inline comments that describe *what* the code does — only *why* when it's non-obvious.

## Reporting issues

Open a GitHub issue with a minimal reproduction case.
