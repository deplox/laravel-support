<?php

declare(strict_types=1);

namespace Deplox\Support\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Routing\RedirectController;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Routing\ViewController;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionFunction;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;

final class RouteShowCommand extends Command
{
    protected $name = 'route:show';

    protected $description = 'Show all registered routes';

    /** @var list<string> */
    private array $headers = ['Domain', 'Method', 'URI', 'Name', 'Action', 'Middleware'];

    /** @var array<string, string> */
    private array $verbColors = [
        'ANY' => 'red',
        'GET' => 'blue',
        'HEAD' => '#6C7280',
        'OPTIONS' => '#6C7280',
        'POST' => 'yellow',
        'PUT' => 'yellow',
        'PATCH' => 'yellow',
        'DELETE' => 'red',
    ];

    public function __construct(private readonly Router $router)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->router->flushMiddlewareGroups();

        $routes = collect($this->router->getRoutes()->getRoutes());

        if ($routes->isEmpty()) {
            $this->components->error("Your application doesn't have any routes.");

            return;
        }

        $routes = $this->compileRoutes($routes);

        if ($routes->isEmpty()) {
            $this->components->error("Your application doesn't have any routes matching the given criteria.");

            return;
        }

        $this->option('json')
            ? $this->forJson($routes)
            : $this->forTable($routes);
    }

    /**
     * @param Collection<int, Route> $routes
     * @return Collection<int, array<string, mixed>>
     */
    private function compileRoutes(Collection $routes): Collection
    {
        return $this->pluckColumns($this->sortRoutes($this->filterRoutes($routes)));
    }

    /**
     * @param array<string, mixed> $route
     */
    private function shouldIncludeRoute(array $route): bool
    {
        $name = $this->stringOption('name');
        if ($name !== null && ! Str::contains((string) $route['name'], $name)) {
            return false;
        }

        $uri = $this->stringOption('uri');
        if ($uri !== null && ! Str::contains((string) $route['uri'], $uri)) {
            return false;
        }

        $method = $this->stringOption('method');
        if ($method !== null && ! Str::contains((string) $route['method'], mb_strtoupper($method))) {
            return false;
        }

        $domain = $this->stringOption('domain');
        if ($domain !== null && ! Str::contains((string) $route['domain'], $domain)) {
            return false;
        }

        if (! $this->option('vendor') && $route['vendor']) {
            return false;
        }

        return true;
    }

    private function stringOption(string $name): ?string
    {
        $value = $this->option($name);

        return is_string($value) ? $value : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function getRouteInformation(Route $route): array
    {
        return [
            'domain' => $route->domain(),
            'method' => $route->methods() === Router::$verbs ? 'ANY' : implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $this->getAction($route),
            'middleware' => $this->getMiddleware($route),
            'vendor' => $this->isVendorRoute($route),
        ];
    }

    /**
     * @param Collection<int, Route> $routes
     * @return Collection<int, array<string, mixed>>
     */
    private function filterRoutes(Collection $routes): Collection
    {
        return $routes
            ->map(fn (Route $route): array => $this->getRouteInformation($route))
            ->filter(fn (array $route): bool => $this->shouldIncludeRoute($route))
            ->values();
    }

    /**
     * @param Collection<int, array<string, mixed>> $routes
     * @return Collection<int, array<string, mixed>>
     */
    private function sortRoutes(Collection $routes): Collection
    {
        $sort = $this->stringOption('sort') ?? 'uri';
        $reverse = (bool) $this->option('reverse');

        if ($sort === 'middleware') {
            $sorted = $routes->sortBy(static function (array $route): string {
                $middleware = (array) $route['middleware'];
                asort($middleware);

                return implode(',', $middleware);
            }, SORT_NUMERIC);
        } else {
            $sorted = $routes->sortBy($sort, SORT_NATURAL);
        }

        return $reverse ? $sorted->reverse()->values() : $sorted->values();
    }

    /**
     * @param Collection<int, array<string, mixed>> $routes
     * @return Collection<int, array<string, mixed>>
     */
    private function pluckColumns(Collection $routes): Collection
    {
        $columns = $this->getColumns();

        return $routes->map(
            fn (array $route): array => array_filter(
                $route,
                static fn (string $key): bool => in_array($key, $columns, true),
                ARRAY_FILTER_USE_KEY
            )
        )->values();
    }

    private function getAction(Route $route): string
    {
        $action = $route->getActionMethod();
        $controller = $route->getControllerClass();

        if ($action === $controller) {
            return match ($controller) {
                RedirectController::class => 'Redirect',
                ViewController::class => 'View',
                default => 'Invokable',
            };
        }

        return $action;
    }

    /**
     * @return array<int, string>
     */
    private function getMiddleware(Route $route, bool $useShortHand = true): array
    {
        $map = array_flip($this->router->getMiddleware());

        return collect($this->router->gatherRouteMiddleware($route))
            ->map(function (mixed $middleware) use ($map, $useShortHand): string {
                $middleware = $middleware instanceof Closure ? 'Closure' : (string) $middleware;

                if ($useShortHand) {
                    $key = Str::before($middleware, ':');

                    if (Arr::exists($map, $key)) {
                        $middleware = Str::replace($key, $map[$key], $middleware);
                    }
                }

                return $middleware;
            })
            ->values()
            ->all();
    }

    private function isVendorRoute(Route $route): bool
    {
        if ($route->action['uses'] instanceof Closure) {
            $path = new ReflectionFunction($route->action['uses'])->getFileName();
        } elseif (is_string($route->action['uses']) && str_contains($route->action['uses'], 'SerializableClosure')) {
            return false;
        } elseif (is_string($route->action['uses'])) {
            if ($this->isFrameworkController($route)) {
                return false;
            }

            $controllerClass = $route->getControllerClass();

            if ($controllerClass === null || ! class_exists($controllerClass)) {
                return false;
            }

            $path = new ReflectionClass($controllerClass)->getFileName();
        } else {
            return false;
        }

        return is_string($path) && str_starts_with($path, $this->laravel->basePath('vendor'));
    }

    private function isFrameworkController(Route $route): bool
    {
        return in_array($route->getControllerClass(), [RedirectController::class, ViewController::class], true);
    }

    /** @return list<string> */
    private function getColumns(): array
    {
        return array_map('strtolower', $this->headers);
    }

    /** @param Collection<int, array<string, mixed>> $routes */
    private function forJson(Collection $routes): void
    {
        $this->line($routes->values()->toJson());
    }

    /** @param Collection<int, array<string, mixed>> $routes */
    private function forTable(Collection $routes): void
    {
        $table = new Table($this->output);

        $table->setHeaders(['Method', 'URI', 'Name', 'Action', 'Middleware']);

        $rows = $routes->map(fn (array $route): array => [
            'method' => $this->formatMethod($route),
            'uri' => $this->formatUri($route),
            'name' => $route['name'],
            'action' => $this->formatAction($route),
            'middleware' => implode(', ', (array) $route['middleware']),
        ])->all();

        $table->setRows($rows);
        $table->render();
    }

    /** @param array<string, mixed> $route */
    private function formatMethod(array $route): string
    {
        return Str::of((string) $route['method'])
            ->explode('|')
            ->map(fn (string $method): string => sprintf('<fg=%s>%s</>', $this->verbColors[$method] ?? 'default', $method))
            ->implode('<fg=#6C7280>|</>');
    }

    /** @param array<string, mixed> $route */
    private function formatUri(array $route): string
    {
        $uri = (string) $route['uri'];

        if ($route['domain']) {
            $uri = $route['domain'].'/'.mb_ltrim($uri, '/');
        }

        return (string) preg_replace('#({[^}]+})#', '<fg=yellow>$1</>', $uri);
    }

    /** @param array<string, mixed> $route */
    private function formatAction(array $route): string
    {
        $action = (string) $route['action'];

        if (in_array($action, ['Closure', 'Invokable', 'View', 'Redirect'], true)) {
            return "<fg=yellow>{$action}</>";
        }

        return $action;
    }

    protected function getOptions(): array
    {
        $columns = implode(', ', $this->getColumns());

        return [
            new InputOption('json', null, InputOption::VALUE_NONE, 'Output routes as JSON'),
            new InputOption('domain', null, InputOption::VALUE_OPTIONAL, 'Filter routes by domain'),
            new InputOption('method', null, InputOption::VALUE_OPTIONAL, 'Filter routes by method'),
            new InputOption('name', null, InputOption::VALUE_OPTIONAL, 'Filter routes by name'),
            new InputOption('uri', null, InputOption::VALUE_OPTIONAL, 'Filter routes by uri'),
            new InputOption('sort', null, InputOption::VALUE_OPTIONAL, 'Sort routes by column ('.$columns.')', 'uri'),
            new InputOption('reverse', null, InputOption::VALUE_NONE, 'Reverse the sort order of the routes'),
            new InputOption('vendor', null, InputOption::VALUE_NONE, 'Include routes defined by vendor packages'),
        ];
    }
}
