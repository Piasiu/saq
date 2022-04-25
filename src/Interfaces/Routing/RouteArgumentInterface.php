<?php
namespace Saq\Interfaces\Routing;

interface RouteArgumentInterface
{
    /**
     * @return string
     */
    function getPattern(): string;

    /**
     * @param string $value
     * @return bool
     */
    function isValid(string $value): bool;

    /**
     * @param string $value
     * @return mixed
     */
    function filter(string $value): mixed;
}