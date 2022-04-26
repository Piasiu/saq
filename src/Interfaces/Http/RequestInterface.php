<?php
namespace Saq\Interfaces\Http;

interface RequestInterface
{
    /**
     * @return string
     */
    function getMethod(): string;

    /**
     * @return string
     */
    function getScheme(): string;

    /**
     * @return string
     */
    function getHost(): string;

    /**
     * @return int
     */
    function getPort(): int;

    /**
     * @return string
     */
    function getUri(): string;

    /**
     * @return array
     */
    function getQueryParams(): array;

    /**
     * @return RequestBodyInterface
     */
    function getBody(): RequestBodyInterface;

    /**
     * @return string
     */
    function getRemoteIp(): string;

    /**
     * @return string
     */
    function getRemotePort(): string;

    /**
     * @return string|null
     */
    function getRemoteHost(): ?string;

    /**
     * @return string|null
     */
    function getReferer(): ?string;

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