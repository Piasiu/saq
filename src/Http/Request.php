<?php
namespace Saq\Http;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Http\RequestBodyInterface;
use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\UriInterface;

class Request implements RequestInterface
{
    /**
     * @var string
     */
    private string $method;

    /**
     * @var UriInterface
     */
    private UriInterface $uri;

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
     * @var string|null
     */
    private ?string $remoteHost = null;

    /**
     * @var string|null
     */
    private ?string $referer = null;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setUri();
        $this->body = new RequestBody();
        $this->remoteIp = $_SERVER['REMOTE_ADDR'];

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
    public function getUri(): UriInterface
    {
        return $this->uri;
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
    public function getRemoteHost(?string $default = null): ?string
    {
        return $this->remoteHost !== null ? $this->remoteHost : $default;
    }

    /**
     * @inheritDoc
     */
    public function getReferer(?string $default = null): ?string
    {
        return $this->referer !== null ? $this->referer : $default;
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

    private function setUri(): void
    {
        [$path] = explode('?', $_SERVER['REQUEST_URI']);
        $this->uri = new Uri($_SERVER['SERVER_NAME']);
        $this->uri
            ->setScheme($_SERVER['REQUEST_SCHEME'])
            ->setPort($_SERVER['SERVER_PORT'])
            ->setPath($path)
            ->setQueryParams($_GET);
    }
}