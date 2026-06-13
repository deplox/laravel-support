<?php

declare(strict_types=1);

namespace Deplox\Support\Tests\Fixtures;

use Deplox\Support\Concerns\Actionable;

final class GreetAction
{
    use Actionable;

    public function execute(): mixed
    {
        return 'hello';
    }
}
