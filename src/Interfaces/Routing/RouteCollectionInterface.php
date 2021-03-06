<?php
namespace Saq\Interfaces\Routing;

use Saq\Routing\Route;

interface RouteCollectionInterface
{
    /**
     * @param Route $route
     * @return void
     */
    function addRoute(Route $route): void;

    /**
     * @param string $routeName
     * @return Route
     */
    function getRouteByName(string $routeName): Route;
}