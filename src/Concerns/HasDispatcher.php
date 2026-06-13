<?php

declare(strict_types=1);

namespace Deplox\Support\Concerns;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

trait HasDispatcher
{
    protected ?DispatcherContract $dispatcher = null;

    protected function dispatch(mixed $event, mixed ...$params): mixed
    {
        return $this->getDispatcher()->dispatch($event, ...$params);
    }

    protected function getDispatcher(): DispatcherContract
    {
        return $this->dispatcher ??= app(DispatcherContract::class);
    }
}
