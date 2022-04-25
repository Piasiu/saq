<?php
namespace Saq\Interfaces\Handlers;

use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\ResponseInterface;
use Throwable;

interface ErrorHandlerInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    function handle(RequestInterface $request, ResponseInterface $response, Throwable $throwable): ResponseInterface;
}