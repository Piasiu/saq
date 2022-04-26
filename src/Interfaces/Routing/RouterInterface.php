<?php
namespace Saq\Interfaces\Routing;

interface RouterInterface
{
    /**
     * @param string $basePath
     */
    function setBasePath(string $basePath): void;

    /**
     * @return string
     */
    function getBasePath(): string;

    /**
     * @param string $method
     * @param string $uri
     * @return ActionInterface
     */
    function handle(string $method, string $uri): ActionInterface;

    /**
     * @param string $routeName
     * @param array $arguments
     * @param array $queryParams
     * @return string
     */
    function urlFor(string $routeName, array $arguments = [], array $queryParams = []): string;
}