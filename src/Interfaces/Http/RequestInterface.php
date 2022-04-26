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
}