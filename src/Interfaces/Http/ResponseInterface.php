<?php
namespace Saq\Interfaces\Http;

interface ResponseInterface
{
    /**
     * @return string
     */
    function getProtocolVersion(): string;

    /**
     * @return int
     */
    function getStatusCode(): int;

    /**
     * @return string
     */
    function getReasonPhrase(): string;

    /**
     * @return array
     */
    function getHeaders(): array;

    /**
     * @return ResponseBodyInterface
     */
    function getBody(): ResponseBodyInterface;

    /**
     * @param int $code
     * @return ResponseInterface
     */
    function withStatusCode(int $code): ResponseInterface;

    /**
     * @param string $name
     * @param string $value
     * @return ResponseInterface
     */
    function withHeader(string $name, string $value): ResponseInterface;

    /**
     * @param ResponseBodyInterface $body
     * @return ResponseInterface
     */
    function withBody(ResponseBodyInterface $body): ResponseInterface;

    /**
     * @param string $url
     * @param int $status
     * @return ResponseInterface
     */
    function withRedirect(string $url, int $status): ResponseInterface;
}