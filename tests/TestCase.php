<?php

declare(strict_types=1);

namespace Deplox\Support\Tests;

use Deplox\Support\SupportServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [SupportServiceProvider::class];
    }
}
