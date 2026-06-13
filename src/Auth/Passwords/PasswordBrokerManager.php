<?php

declare(strict_types=1);

namespace Deplox\Support\Auth\Passwords;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Contracts\Auth\PasswordBrokerFactory as PasswordBrokerFactoryContract;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application as App;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Hashing\HashManager;
use InvalidArgumentException;

final class PasswordBrokerManager implements PasswordBrokerFactoryContract
{
    /**
     * @param array<string, PasswordBrokerContract> $brokers
     */
    public function __construct(
        private App $app,
        private array $brokers = []
    ) {}

    /**
     * @param array<int, mixed> $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->broker()->{$method}(...$parameters);
    }

    /**
     * Get a password broker instance by name.
     */
    public function broker(mixed $name = null): PasswordBrokerContract
    {
        $brokerName = is_string($name) ? $name : $this->getDefaultDriver();

        return $this->brokers[$brokerName] ?? ($this->brokers[$brokerName] = $this->resolve($brokerName));
    }

    public function getDefaultDriver(): string
    {
        return $this->app->make(ConfigRepository::class)->get('auth.defaults.passwords');
    }

    public function setDefaultDriver(string $name): void
    {
        $this->app->make(ConfigRepository::class)->set('auth.defaults.passwords', $name);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function resolve(string $name): PasswordBrokerContract
    {
        $config = $this->getConfig($name);

        if ($config === null) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        $users = $this->app->make(AuthFactory::class)->createUserProvider($config['provider'] ?? null);

        if ($users === null) {
            throw new InvalidArgumentException("User provider for password broker [{$name}] is not configured.");
        }

        return new PasswordBroker($users, $this->createTokenRepository($config));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createTokenRepository(array $config): DatabaseTokenRepository
    {
        return new DatabaseTokenRepository(
            $this->app->make(ConnectionResolverInterface::class)->connection($config['connection'] ?? null),
            $this->app->make(HashManager::class),
            $this->app->make(ConfigRepository::class)->get('app.key'),
            $config['table'],
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getConfig(string $name): array|null
    {
        return $this->app->make(ConfigRepository::class)->get("auth.passwords.{$name}");
    }
}
