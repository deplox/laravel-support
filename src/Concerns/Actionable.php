<?php

declare(strict_types=1);

namespace Deplox\Support\Concerns;

use Illuminate\Container\Container;

trait Actionable
{
    public function __invoke(mixed ...$arguments): mixed
    {
        return $this->execute(...$arguments);
    }

    /** @param array<string, mixed> $parameters */
    public static function make(array $parameters = []): static
    {
        return Container::getInstance()->make(static::class, $parameters);
    }

    public static function run(mixed ...$arguments): mixed
    {
        return static::make()->execute(...$arguments);
    }

    public function execute(): mixed
    {
        return null;
    }
}
