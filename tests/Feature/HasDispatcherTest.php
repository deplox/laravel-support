<?php

declare(strict_types=1);

use Deplox\Support\Concerns\HasDispatcher;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Events\Dispatcher;

final class EventEmitter
{
    use HasDispatcher;

    public function emit(mixed $event): mixed
    {
        return $this->dispatch($event);
    }

    public function expose(): DispatcherContract
    {
        return $this->getDispatcher();
    }
}

test('getDispatcher() resolves the Dispatcher from the container', function (): void {
    $emitter = new EventEmitter();

    expect($emitter->expose())->toBeInstanceOf(Dispatcher::class);
});

test('getDispatcher() returns the same instance on repeated calls', function (): void {
    $emitter = new EventEmitter();

    expect($emitter->expose())->toBe($emitter->expose());
});

test('an injected dispatcher is used instead of the container', function (): void {
    $custom = Mockery::mock(DispatcherContract::class);
    $custom->shouldReceive('dispatch')->once()->with('my.event')->andReturn(null);

    $emitter = new EventEmitter();

    $prop = new ReflectionProperty($emitter, 'dispatcher');
    $prop->setValue($emitter, $custom);

    $emitter->emit('my.event');
});

test('dispatch() fires listeners registered on the event', function (): void {
    $called = false;

    /** @var Dispatcher $dispatcher */
    $dispatcher = app(DispatcherContract::class);
    $dispatcher->listen('test.dispatched', function () use (&$called): void {
        $called = true;
    });

    $emitter = new EventEmitter();
    $emitter->emit('test.dispatched');

    expect($called)->toBeTrue();
});
