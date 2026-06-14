<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

test('route:show renders a table of registered routes', function (): void {
    Route::get('/hello', fn () => 'world')->name('test.hello');

    $this->artisan('route:show')->assertSuccessful();
});

test('route:show outputs JSON when --json flag is used', function (): void {
    Route::get('/ping', fn () => 'pong')->name('api.ping');

    $this->artisan('route:show', ['--json' => true])
        ->expectsOutputToContain('api.ping')
        ->assertSuccessful();
});

test('route:show filters routes by name', function (): void {
    Route::get('/a', fn () => 'ok')->name('api.users');
    Route::get('/b', fn () => 'ok')->name('web.home');

    $this->artisan('route:show', ['--name' => 'api', '--json' => true])
        ->expectsOutputToContain('api.users')
        ->assertSuccessful();
});

test('route:show filters routes by uri', function (): void {
    Route::get('/ping', fn () => 'ok')->name('route.ping');
    Route::get('/home', fn () => 'ok')->name('route.home');

    $this->artisan('route:show', ['--uri' => 'ping', '--json' => true])
        ->expectsOutputToContain('route.ping')
        ->assertSuccessful();
});

test('route:show filters routes by HTTP method', function (): void {
    Route::get('/a', fn () => 'ok')->name('get.route');
    Route::post('/b', fn () => 'ok')->name('post.route');

    $this->artisan('route:show', ['--method' => 'POST', '--json' => true])
        ->expectsOutputToContain('post.route')
        ->assertSuccessful();
});

test('route:show shows error when no routes match the given filters', function (): void {
    Route::get('/hello', fn () => 'world');

    $this->artisan('route:show', ['--name' => 'nonexistent'])
        ->expectsOutputToContain("doesn't have any routes matching")
        ->assertSuccessful();
});

test('route:show supports reverse sorting', function (): void {
    Route::get('/aaa', fn () => 'ok');
    Route::get('/zzz', fn () => 'ok');

    $this->artisan('route:show', ['--sort' => 'uri', '--reverse' => true, '--json' => true])
        ->assertSuccessful();
});

test('route:show shows error when the application has no routes', function (): void {
    $this->artisan('route:show')
        ->expectsOutputToContain("doesn't have any routes")
        ->assertSuccessful();
});
