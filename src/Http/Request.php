<?php
namespace Saq\Http;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Http\RequestBodyInterface;
use Saq\Interfaces\Http\RequestInterface;

class Request implements RequestInterface
{
    /**
     * @var string
     */
    private string $method;

    /**
     * @var string
     */
    private string $scheme;

    /**
     * @var string
     */
    private string $host;

    /**
     * @var int
     */
    private int $port;

    /**
     * @var string
     */
    private string $uri;

    /**
     * @var array
     */
    private array $queryParams;

    /**
     * @var array
     */
    private array $attributes;

    /**
     * @var RequestBodyInterface
     */
    private RequestBodyInterface $body;

    /**
     * @var string
     */
    private string $remoteIp;

    /**
     * @var string
     */
    private string $remotePort;

    /**
     * @var string|null
     */
    private ?string $remoteHost = null;

    /**
     * @var string|null
     */
    private ?string $referer = null;

    #[Pure]
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->host = $_SERVER['SERVER_NAME'];
        $this->port = $_SERVER['SERVER_PORT'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->queryParams = $_REQUEST;
        $this->body = new RequestBody();
        $this->remoteIp = $_SERVER['REMOTE_ADDR'];
        $this->remotePort = $_SERVER['REMOTE_PORT'];

        if (array_key_exists('REMOTE_HOST', $_SERVER))
        {
            $this->remoteHost = $_SERVER['REMOTE_HOST'];
        }

        if (array_key_exists('HTTP_REFERER', $_SERVER))
        {
            $this->referer = $_SERVER['HTTP_REFERER'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): RequestBodyInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function getRemoteIp(): string
    {
        return $this->remoteIp;
    }

    /**
     * @inheritDoc
     */
    public function getRemotePort(): string
    {
        return $this->remotePort;
    }

    /**
     * @inheritDoc
     */
    public function getRemoteHost(): ?string
    {
        return $this->remoteHost;
    }

    /**
     * @inheritDoc
     */
    public function getReferer(): ?string
    {
        return $this->referer;
    }

    /**
     * @inheritDoc
     */
    public function setAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->hasAttribute($name) ? $this->attributes[$name] : $default;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}