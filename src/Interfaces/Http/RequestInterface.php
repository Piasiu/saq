<?php
namespace Saq\Interfaces\Http;

interface RequestInterface
{
    /**
     * @return string
     */
    function getMethod(): string;

    /**
     * @return UriInterface
     */
    function getUri(): UriInterface;

    /**
     * @return RequestBodyInterface
     */
    function getBody(): RequestBodyInterface;

    /**
     * @return string
     */
    function getRemoteIp(): string;

    /**
     * @param string|null $default
     * @return string|null
     */
    function getRemoteHost(?string $default = null): ?string;

    /**
     * @param string|null $default
     * @return string|null
     */
    function getReferer(?string $default = null): ?string;

    /**
     * @param string $name
     * @param mixed $value
     */
    function setAttribute(string $name, mixed $value): void;

    /**
     * @param string $name
     * @return bool
     */
    function hasAttribute(string $name): bool;

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
    function getAttribute(string $name, mixed $default = null): mixed;

    /**
     * @return array
     */
    function getAttributes(): array;
}