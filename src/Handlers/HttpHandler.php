<?php
namespace Saq\Handlers;

use Saq\Http\ResponseBody;
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
        if (in_array('application/json', $request->getHeader('Accept')))
        {
            $response->withHeader('Content-Type', 'application/json');
            $content = json_encode([
                'httpStatusCode' => $this->httpStatusCode,
                'description' => $this->description
            ]);
        }
        else
        {
            $content = "<!DOCTYPE html><html lang=\"en\"><head></head><meta charset=\"UTF-8\"/></head><body style=\"background-color: #1b1b1b; text-align: center;\">";
            $content .= "<h1 style=\"color: #fc5433;\">SAQ $this->httpStatusCode</h1>";
            $content .= "<p style=\"font-size: 1.4rem; color: white;\">$this->description</p>";
            $content .= '</body></html>';
        }

        $body = new ResponseBody($content);
        return $response->withBody($body)->withStatusCode($this->httpStatusCode);
    }
}