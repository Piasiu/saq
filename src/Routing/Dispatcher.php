<?php
namespace Saq\Routing;

class Dispatcher
{
    /**
     * @var Route[][]
     */
    private array $staticRoutes = [];

    /**
     * @var Route[][]
     */
    private array $dynamicRoutes = [];

    /**
     * @param Route $route
     */
    public function addRoute(Route $route): void
    {
        if ($route->hasArguments())
        {
            foreach ($route->getMethods() as $method)
            {
                $pattern = str_replace('/', '\/', $route->getPattern());
                $pattern = sprintf('/^%s$/', $pattern);
                $this->dynamicRoutes[$method][$pattern] = $route;
            }
        }
        else
        {
            foreach ($route->getMethods() as $method)
            {

                $this->staticRoutes[$method][$route->getPattern()] = $route;
            }
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @return array
     */
    public function handle(string $method, string $uri): array
    {
        if (isset($this->staticRoutes[$method][$uri]))
        {
            return [$this->staticRoutes[$method][$uri], []];
        }

        return $this->dispatchDynamicRoute($method, $uri);
    }

    /**
     * @param string $uri
     * @return array
     */
    public function getAllowedMethods(string $uri): array
    {
        // TODO
        return [];
    }

    /**
     * @param string $method
     * @param string $uri
     * @return array [Route|null, array]
     */
    private function dispatchDynamicRoute(string $method, string $uri): array
    {
        foreach ($this->dynamicRoutes[$method] as $pattern => $route)
        {
            if (preg_match($pattern, $uri, $matches))
            {
                $arguments = [];

                foreach ($route->getArguments() as $name => $argument)
                {
                    $arguments[$name] = $argument->filter($matches[$name]);
                }

                return [$route, $arguments];
            }
        }

        return [null, []];
    }
}