<?php

declare(strict_types=1);

namespace Deplox\Support\Tests\Fixtures;

use Deplox\Support\Concerns\HasDispatcher;

final class EventEmitter
{
    use HasDispatcher;
}
