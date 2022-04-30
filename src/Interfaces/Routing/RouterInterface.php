<?php
namespace Saq\Interfaces\Routing;

use Saq\Interfaces\Http\RequestInterface;
use Saq\Routing\Route;

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
     * @param RequestInterface $request
     * @return Route|null
     */
    function handle(RequestInterface $request): ?Route;

    /**
     * @param string $routeName
     * @param array $arguments
     * @param array $queryParams
     * @return string
     */
    function urlFor(string $routeName, array $arguments = [], array $queryParams = []): string;
}