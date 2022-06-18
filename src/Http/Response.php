<?php
namespace Saq\Http;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Http\ResponseBodyInterface;
use Saq\Interfaces\Http\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var ResponseBodyInterface
     */
    private ResponseBodyInterface $body;

    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var string
     */
    private string $protocolVersion;

    /**
     * @var string[]
     */
    public static array $reasonPhrases = [
        200 => 'OK',
        404 => 'NOT FOUND'
    ];

    /**
     * @var array
     */
    private array $headers = [];

    /**
     * @param ResponseBody|null $body
     * @param int $statusCode
     * @param string $protocolVersion
     */
    public function __construct(?ResponseBody $body = null, int $statusCode = 200, string $protocolVersion = '1.1')
    {
        if ($body === null)
        {
            $body = new ResponseBody();
        }

        $this->statusCode = $statusCode;
        $this->protocolVersion = $protocolVersion;
        $this->withStatusCode($statusCode)->withBody($body);
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function withStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function getReasonPhrase(): string
    {
        if (array_key_exists($this->statusCode, self::$reasonPhrases))
        {
            return self::$reasonPhrases[$this->statusCode];
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name][] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): ResponseBodyInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(ResponseBodyInterface $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withRedirect(string $url, int $status = 200): self
    {
        $this->withHeader('Location', $url)->withStatusCode($status);
    }
}