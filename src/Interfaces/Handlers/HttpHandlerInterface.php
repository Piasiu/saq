<?php
namespace Saq\Interfaces\Handlers;

use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\ResponseInterface;

interface HttpHandlerInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    function handle(RequestInterface $request, ResponseInterface $response): ResponseInterface;
}