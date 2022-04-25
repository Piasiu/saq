<?php
namespace Saq\Interfaces\Routing;

interface ActionInterface
{
    /**
     * @return bool
     */
    function exists(): bool;

    /**
     * @return callable|null
     */
    function getCallable(): ?callable;

    /**
     * @return array
     */
    function getArguments(): array;
}