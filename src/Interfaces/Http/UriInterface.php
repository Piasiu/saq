<?php
namespace Saq\Interfaces\Http;

use Stringable;

interface UriInterface extends Stringable
{
    /**
     * @param string $scheme
     * @return UriInterface
     */
    function setScheme(string $scheme): UriInterface;

    /**
     * @return string
     */
    function getScheme(): string;

    /**
     * @param string $username
     * @param string|null $password
     * @return UriInterface
     */
    function setUserInfo(string $username, ?string $password = null): UriInterface;

    /**
     * @return string
     */
    function getUserInfo(): string;

    /**
     * @param string $host
     * @return UriInterface
     */
    function setHost(string $host): UriInterface;

    /**
     * @return string
     */
    function getHost(): string;

    /**
     * @param int $port
     * @return UriInterface
     */
    function setPort(int $port): UriInterface;

    /**
     * @return int
     */
    function getPort(): int;

    /**
     * @return string
     */
    function getAuthority(): string;

    /**
     * @param string $path
     * @return UriInterface
     */
    function setPath(string $path): UriInterface;

    /**
     * @return string
     */
    function getPath(): string;

    /**
     * @param array $params
     * @return UriInterface
     */
    function setQueryParams(array $params): UriInterface;

    /**
     * @return array
     */
    function getQueryParams(): array;

    /**
     * @return string
     */
    function getQuery(): string;
}