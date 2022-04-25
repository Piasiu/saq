<?php
namespace Saq\Routing;

use JetBrains\PhpStorm\NoReturn;
use RuntimeException;
use Saq\Exceptions\Runtime\ClassNotImplementInterfaceException;
use Saq\Interfaces\Routing\ActionInterface;
use Saq\Interfaces\Routing\CallableResolverInterface;
use Saq\Interfaces\Routing\RouteCollectionInterface;
use Saq\Interfaces\Routing\RouteCollectorInterface;
use Saq\Interfaces\Routing\RouterInterface;

class Router implements RouterInterface, RouteCollectionInterface
{
    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * @var RouteCollectorInterface
     */
    private RouteCollectorInterface $routeCollector;

    /**
     * @var CallableResolverInterface
     */
    private CallableResolverInterface $callableResolver;

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
     */
    #[NoReturn]
    public function __construct(CallableResolverInterface $callableResolver, array $options = [])
    {
        $this->callableResolver = $callableResolver;
        $this->dispatcher = new Dispatcher();
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
    public function handle(string $method, string $uri): ActionInterface
    {
        $action = new Action();
        list($route, $arguments) = $this->dispatcher->handle($method, $uri);

        /** @var Route $route */
        if ($route !== null)
        {
            $callable = $this->callableResolver->resolve($route->getRawCallable());
            $action->set($callable, $arguments);
        }

        return $action;
    }

    /**
     * @inheritDoc
     */
    public function pathFor(string $routeName, array $arguments = [], array $queryParams = []): string
    {
        if (!isset($this->routes[$routeName]))
        {
            throw new RuntimeException("Route \"{$routeName}\" does not exist.");
        }

        $route = $this->routes[$routeName];
        $arguments = array_merge($route->getDefaults(), $arguments);
        $routeArguments = $route->getArguments();
        $url = $route->getPath();

        foreach ($routeArguments as $name => $routeArgument)
        {
            if (!array_key_exists($name, $arguments))
            {
                throw new RuntimeException("Argument \"{$name}\" of route \"{$route->getName()}\" is missing.");
            }

            if (!$routeArgument->isValid($arguments[$name]))
            {
                throw new RuntimeException("Given value \"{$arguments[$name]}\" for argument \"{$name}\" of route \"{$route->getName()}\" is invalid.");
            }

            $url = str_replace('{'.$name.'}', $arguments[$name], $url);
        }

        /* TODO segments in path
        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }
        $url = implode('', $segments);
        */

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
        if (array_key_exists('basePath', $options))
        {
            $this->basePath = $options['basePath'];
        }

        if (array_key_exists('routeCollector', $options))
        {
            $routeCollector = $options['routeCollector'];

            if (!($routeCollector instanceof RouteCollectorInterface))
            {
                throw new ClassNotImplementInterfaceException(get_class($routeCollector), RouteCollectorInterface::class);
            }

            $this->routeCollector = $routeCollector;
        }
        else
        {
            $this->routeCollector = new RouteCollector();
        }

        if (array_key_exists('controllersPath', $options))
        {
            $this->routeCollector->setPath($options['controllersPath']);
        }

        if (array_key_exists('controllerNamePattern', $options))
        {
            $this->routeCollector->setPattern($options['controllerNamePattern']);
        }

        if (array_key_exists('cacheFile', $options))
        {
            $this->routeCollector->setCacheFile($options['cacheFile']);
        }
    }
}