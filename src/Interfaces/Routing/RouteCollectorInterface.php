<?php
namespace Saq\Interfaces\Routing;

interface RouteCollectorInterface
{
    /**
     * @param RouteCollectionInterface $routeCollection
     */
    function collect(RouteCollectionInterface $routeCollection): void;
}