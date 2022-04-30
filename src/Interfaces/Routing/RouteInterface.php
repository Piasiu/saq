<?php
namespace Saq\Interfaces\Routing;

interface RouteInterface
{
    /**
     * @return string
     */
    function getName(): string;

    /**
     * @return string
     */
    function getPath(): string;

    /**
     * @return array
     */
    function getMethods(): array;

    /**
     * @return RouteArgumentInterface[]
     */
    function getArguments(): array;

    /**
     * @return array
     */
    function getDefaults(): array;

    /**
     * @return string
     */
    function getPattern(): string;

    /**
     * @return callable|null
     */
    function getCallable(): ?callable;
}