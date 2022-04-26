<?php
namespace Saq\Interfaces\Routing;

interface ActionInterface
{
    /**
     * @return string|null
     */
    function getRouteName(): ?string;

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