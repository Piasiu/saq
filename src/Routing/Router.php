<?php
namespace Saq\Routing;

use JetBrains\PhpStorm\NoReturn;
use ReflectionException;
use RuntimeException;
use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Routing\CallableResolverInterface;
use Saq\Interfaces\Routing\RouteCollectionInterface;
use Saq\Interfaces\Routing\RouterInterface;

class Router implements RouterInterface, RouteCollectionInterface
{
    /**
     * @var CallableResolverInterface
     */
    private CallableResolverInterface $callableResolver;

    /**
     * @var RouteParser
     */
    private RouteParser $routeParser;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * @var RouteCollector
     */
    private RouteCollector $routeCollector;

    /**
     * @var string
     */
    private string $basePath = '';

    /**
     * @var Route[]
     */
    private array $routes = [];

    /**
     * @param CallableResolverInterface $callableResolver
     * @param array $options
     * @throws ReflectionException
     */
    #[NoReturn]
    public function __construct(CallableResolverInterface $callableResolver, array $options = [])
    {
        $this->callableResolver = $callableResolver;
        $this->routeParser = new RouteParser();
        $this->dispatcher = new Dispatcher();
        $this->routeCollector = new RouteCollector();
        $this->setOptions($options);
        $this->routeCollector->collect($this);
    }

    /**
     * @inheritDoc
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @inheritDoc
     */
    public function addRoute(Route $route): void
    {
        $this->routes[$route->getName()] = $route;
        $this->dispatcher->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function getRouteByName(string $routeName): Route
    {
        if (!isset($this->routes[$routeName]))
        {
            throw new RuntimeException("Route \"{$routeName}\" does not exist.");

        }

        return $this->routes[$routeName];
    }

    /**
     * @inheritDoc
     */
    public function handle(RequestInterface $request): ?Route
    {
        $route = $this->dispatcher->handle($request->getMethod(), $request->getUri()->getPath());

        /** @var Route $route */
        if ($route !== null)
        {
            $callable = $this->callableResolver->resolve($route->getRawCallable());
            $route->setCallable($callable);

            foreach ($route->getMiddlewareList() as $middleware)
            {
                $callable = $this->callableResolver->resolve($middleware->getRawCallable());
                $route->addCallableMiddleware($callable);
            }

            return $route;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function urlFor(string $routeName, array $arguments = [], array $queryParams = []): string
    {
        $route = $this->getRouteByName($routeName);
        $url = $this->routeParser->urlFor($route, $arguments);
        $url = $this->basePath.$url;

        if (count($queryParams))
        {
            $url .= '?'.http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        if (isset($options['basePath']) && $options['basePath'] !== false)
        {
            $this->basePath = $options['basePath'];
        }

        if (isset($options['controllersPath']) && $options['controllersPath'] !== false)
        {
            $this->routeCollector->setPath($options['controllersPath']);
        }

        if (isset($options['controllerNamePattern']) && $options['controllerNamePattern'] !== false)
        {
            $this->routeCollector->setPattern($options['controllerNamePattern']);
        }

        if (isset($options['cache']) && $options['cache'] !== false)
        {
            $this->routeCollector->setCacheFile($options['cache']);
        }
    }
}