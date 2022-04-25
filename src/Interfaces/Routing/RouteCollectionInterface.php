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
}