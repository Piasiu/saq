<?php
namespace Saq\Interfaces\Http;

interface RequestInterface
{
    /**
     * @return string
     */
    function getMethod(): string;

    /**
     * @return bool
     */
    function isPost(): bool;

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
     * @param string $name
     * @return bool
     */
    function hasParam(string $name): bool;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
     function getParam(string $name, mixed $default = null): mixed;

    /**
     * @return array
     */
    function getParams(): array;

    /**
     * @return UploadedFileInterface[]
     */
    function getUploadedFiles(): array;

    /**
     * @param string|null $default
     * @return string|null
     */
    function getReferer(?string $default = null): ?string;

    /**
     * @return string[][]
     */
    function getHeaders(): array;

    /**
     * @return string[]
     */
    public function getAcceptLanguages(): array;

    /**
     * @param string $name
     * @return string[]
     */
    function getHeader(string $name): array;

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