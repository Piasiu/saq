<?php
namespace Saq\Routing;

class Dispatcher
{
    /**
     * @var Route[][]
     */
    private array $staticRoutes = [];

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
                    $this->staticRoutes[$method][$segment->getFullPattern()] = $route;
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
        if (isset($this->staticRoutes[$method][$uri]))
        {
            $route = $this->staticRoutes[$method][$uri];
            $route->setArguments($route->getAllDefaults());
            return $route;
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
                $route = $segment->getRoute();
                $arguments = $route->getAllDefaults();

                foreach ($segment->getAllArguments() as $name => $argument)
                {
                    $arguments[$name] = $matches[$name];
                }

                $filteredArguments = [];

                foreach ($route->getAllArguments() as $name => $argument)
                {
                    $filteredArguments[$name] = $argument->filter($arguments[$name]);
                }

                $route->setArguments($filteredArguments);
                return $route;
            }
        }

        return null;
    }

    private function prepareArguments(RouteSegment $segment): void
    {

    }
}