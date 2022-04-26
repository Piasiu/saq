<?php
namespace Saq\Interfaces\Routing;

interface ActionInterface
{
    /**
     * @return RouteInterface|null
     */
    function getRoute(): ?RouteInterface;

    /**
     * @return callable|null
     */
    function getCallable(): ?callable;

    /**
     * @return array
     */
    function getArguments(): array;

    /**
     * @return bool
     */
    function exists(): bool;
}