<?php
namespace Saq\Handlers;

use Saq\Interfaces\Handlers\HttpHandlerInterface;
use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\ResponseInterface;

class HttpHandler implements HttpHandlerInterface
{
    public static array $statusDescription = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed'
    ];

    /**
     * @var int
     */
    private int $httpStatusCode;

    /**
     * @var string
     */
    private string $description = 'HTTP status';

    /**
     * @param int $httpStatusCode
     */
    public function __construct(int $httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;

        if (isset(HttpHandler::$statusDescription[$this->httpStatusCode]))
        {
            $this->description = HttpHandler::$statusDescription[$this->httpStatusCode];
        }
    }

    /**
     * @inheritDoc
     */
    public function handle(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $response->getBody();
        $body->write("<h1>{$this->description} {$this->httpStatusCode}</h1>");
        return $response->withStatusCode($this->httpStatusCode);
    }
}