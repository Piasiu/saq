<?php
namespace Saq\Routing;

class Dispatcher
{
    /**
     * @var Route[][]
     */
    private array $routes = [];

    /**
     * @var RouteSegment[][]
     */
    private array $segments = [];

    /**
     * @param Route $route
     */
    public function addRoute(Route $route): void
    {
        foreach ($route->getSegments() as $segment)
        {
            if ($segment->hasArguments())
            {
                foreach ($route->getMethods() as $method)
                {
                    $pattern = str_replace('/', '\/', $segment->getFullPattern());
                    $pattern = sprintf('/^%s$/', $pattern);
                    // TODO jeśli pattern istnieje to wywalić wyjątek
                    $this->segments[$method][$pattern] = $segment;
                }
            }
            else
            {
                foreach ($route->getMethods() as $method)
                {
                    // TODO jeśli pattern istnieje to wywalić wyjątek
                    $this->routes[$method][$segment->getFullPattern()] = $route;
                }
            }
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @return Route|null
     */
    public function handle(string $method, string $uri): ?Route
    {
        if (isset($this->routes[$method][$uri]))
        {
            return $this->routes[$method][$uri];
        }

        return $this->getMatchedRoute($method, $uri);
    }

    /**
     * @param string $method
     * @param string $uri
     * @return Route|null
     */
    private function getMatchedRoute(string $method, string $uri): ?Route
    {
        foreach ($this->segments[$method] as $pattern => $segment)
        {
            if (preg_match($pattern, $uri, $matches))
            {
                $arguments = $segment->getAllDefaults();

                foreach ($segment->getAllArguments() as $name => $argument)
                {
                    $arguments[$name] = $argument->filter($matches[$name]);
                }

                $route = $segment->getRoute();
                $route->setArguments($arguments);
                return $route;
            }
        }

        return null;
    }
}