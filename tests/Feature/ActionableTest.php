<?php

declare(strict_types=1);

use Deplox\Support\Concerns\Actionable;

final class DoubleAction
{
    use Actionable;

    public function execute(): mixed
    {
        return 42;
    }
}

final class EchoAction
{
    use Actionable;

    public function execute(mixed ...$args): mixed
    {
        return $args[0] ?? null;
    }
}

test('make() resolves a fresh instance via the container', function (): void {
    $instance = DoubleAction::make();

    expect($instance)->toBeInstanceOf(DoubleAction::class);
});

test('run() creates an instance and calls execute with arguments', function (): void {
    expect(EchoAction::run('hello'))->toBe('hello');
});

test('__invoke() delegates to execute()', function (): void {
    $action = new DoubleAction();

    expect($action())->toBe(42);
});

test('execute() returns null by default when not overridden', function (): void {
    $action = new class {
        use Actionable;
    };

    expect($action->execute())->toBeNull();
});
