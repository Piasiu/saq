<?php
namespace Saq\Utils;

use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\ResponseInterface;

class SessionMiddleware
{
    /**
     * @var int
     */
    private int $maxLiveTime;

    /**
     * SessionMiddleware constructor.
     * @param int $maxLiveTime
     */
    public function __construct(int $maxLiveTime)
    {
        $this->maxLiveTime = $maxLiveTime;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        ini_set('session.gc_maxlifetime', $this->maxLiveTime);
        session_set_cookie_params($this->maxLiveTime);
        session_start();
        return $next($request, $response);
    }
}