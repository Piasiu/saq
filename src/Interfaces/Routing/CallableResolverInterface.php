<?php
namespace Saq\Interfaces\Routing;

interface CallableResolverInterface
{
    /**
     * @param callable|array|string $toResolve
     * @return callable
     */
    function resolve(callable|array|string $toResolve): callable;
}