<?php
namespace Saq\Http;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Http\UriInterface;

class Uri implements UriInterface
{
    /**
     * @var string
     */
    private string $scheme = 'http';

    /**
     * @var string
     */
    private string $host;

    /**
     * @var string|null
     */
    private ?string $username = null;

    /**
     * @var string|null
     */
    private ?string $password = null;

    /**
     * @var string|null
     */
    private ?string $userInfo = null;

    /**
     * @var int
     */
    private int $port = 80;
    
    /**
     * @var string
     */
    private string $path = '/';

    /**
     * @var string|null
     */
    private ?string $query = null;

    /**
     * @var array
     */
    private array $queryParams = [];
    
    public function __construct(string $host)
    {
        $this->host = $host;
    }

    /**
     * @inheritDoc
     */
    public function setScheme(string $scheme): UriInterface
    {
        $this->scheme = $scheme;
        return $this;
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
    public function setUserInfo(string $username, ?string $password = null): UriInterface
    {
        $this->username = $username;
        $this->password = $password;
        $this->userInfo = null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        if ($this->userInfo === null)
        {
            $userInfo = '';

            if ($this->username !== null)
            {
                $userInfo .= $this->username;
            }

            if ($this->password !== null)
            {
                $userInfo .= ':'.$this->password;
            }

            $this->userInfo = strlen($userInfo) > 0 ? $userInfo.'@' : '';
        }

        return $this->userInfo;
    }

    /**
     * @inheritDoc
     */
    public function setHost(string $host): UriInterface
    {
        $this->host = $host;
        return $this;
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
    public function setPort(int $port): UriInterface
    {
        $this->port = $port;
        return $this;
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
    public function getAuthority(): string
    {
        $authority = $this->getUserInfo().$this->host;

        if ($this->port !== null && $this->port !== 80)
        {
            $authority .= ':'.$this->port;
        }

        return $authority;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path): UriInterface
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setQueryParams(array $params): UriInterface
    {
        $this->queryParams = $params;
        $this->query = null;
        return $this;
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
    public function getQuery(): string
    {
        if ($this->query === null)
        {
            if (count($this->queryParams) === 0)
            {
                $this->query = '';
            }
            else
            {
                $parts = [];

                foreach ($this->queryParams as $name => $value)
                {
                    $parts[] = "{$name}={$value}";
                }

                $this->query = '?'.implode('&', $parts);
            }
        }

        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return "{$this->getScheme()}://{$this->getAuthority()}{$this->getPath()}{$this->getQuery()}";
    }
}