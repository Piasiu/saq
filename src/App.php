<?php
namespace Saq;

use ReflectionException;
use RuntimeException;
use Saq\Exceptions\Http\HttpException;
use Saq\Exceptions\Http\NotFoundException;
use Saq\Handlers\ErrorHandler;
use Saq\Handlers\HttpHandler;
use Saq\Http\Response;
use Saq\Http\ResponseEmiter;
use Saq\Interfaces\ContainerInterface;
use Saq\Interfaces\Handlers\ErrorHandlerInterface;
use Saq\Interfaces\Handlers\HttpHandlerInterface;
use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\ResponseInterface;
use Throwable;

class App
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var ErrorHandlerInterface|null
     */
    private ?ErrorHandlerInterface $errorHandler = null;

    /**
     * @var HttpHandlerInterface[]
     */
    private array $httpHandlers = [];

    /**
     * @var callable[]
     */
    private array $middlewareList = [];

    /**
     * @param ContainerInterface|array $container
     * @throws ReflectionException
     */
    public function __construct(ContainerInterface|array $container = [])
    {
        $this->container = is_array($container) ? new Container($container) : $container;
    }

    /**
     * @param int $maxLifeTime
     */
    public function runSession(int $maxLifeTime = 86400): void
    {
        ini_set('session.gc_maxlifetime', $maxLifeTime);
        session_set_cookie_params($maxLifeTime);
        session_start();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param callable $middleware
     */
    public function addMiddleware(callable $middleware): void
    {
        $this->middlewareList[] = $middleware;
    }

    /**
     * @param RequestInterface|null $request
     */
    public function run(?RequestInterface $request = null): void
    {
        if ($request !== null)
        {
            $this->container->setRequest($request);
        }

        $request = $this->container->getRequest();
        $response = new Response();

        try
        {
            ob_start();
            $response = $this->handle($request, $response);
        }
        catch (HttpException $httpException)
        {
            $httpHandler = $this->getHttpHandler($httpException->getCode());
            $response = $httpHandler->handle($request, $response);
        }
        catch (Throwable $throwable)
        {
            $response = $this->getErrorHandler()->handle($request, $response, $throwable);
        }
        finally
        {
            $output = ob_get_clean();
        }

        if ($output !== false && $response->getStatusCode() === 200)
        {
            $prependOutputBuffering = $this->container->getSettings()->get('prependOutputBuffering', false);
            $response->getBody()->write($output, $prependOutputBuffering);
        }

        $responseEmiter = new ResponseEmiter();
        $responseEmiter->emit($response);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws NotFoundException
     */
    private function handle(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $route = $this->container->getRouter()->handle($request);

        if ($route === null)
        {
            throw new NotFoundException();
        }

        $request->setAttribute('route', $route);
        $middlewareList = array_merge($this->middlewareList, $route->getCallableMiddlewareList());

        $last = static function (RequestInterface $request, ResponseInterface $response) use ($route): ResponseInterface {
            $arguments = array_merge([$request, $response], $route->getArguments());
            return call_user_func_array($route->getCallable(), $arguments);
        };

        foreach ($middlewareList as $middleware)
        {
            $next = $last;
            $last = static function (RequestInterface $request, ResponseInterface $response) use ($middleware, $next) {
                return $middleware($request, $response, $next);
            };
        }

        return $last($request, $response);
    }

    /**
     * @param ErrorHandlerInterface $handler
     * @return App
     */
    public function setErrorHandler(ErrorHandlerInterface $handler): App
    {
        $this->errorHandler = $handler;
        return $this;
    }

    /**
     * @param int $httpStatusCode
     * @param HttpHandlerInterface $handler
     * @return App
     */
    public function setHttpHandler(int $httpStatusCode, HttpHandlerInterface $handler): App
    {
        if ($httpStatusCode === 200)
        {
            throw new RuntimeException('Unable to set handler for status code 200.');
        }

        $this->httpHandlers[$httpStatusCode] = $handler;
        return $this;
    }

    /**
     * @return ErrorHandlerInterface
     */
    private function getErrorHandler(): ErrorHandlerInterface
    {
        if ($this->errorHandler === null)
        {
            $settings = $this->container->getSettings();
            $displayDetails = $settings->get('displayErrorDetails', false);
            $this->errorHandler = new ErrorHandler($displayDetails);
        }

        return $this->errorHandler;
    }

    /**
     * @param int $httpStatusCode
     * @return HttpHandlerInterface
     */
    private function getHttpHandler(int $httpStatusCode): HttpHandlerInterface
    {
        if (!isset($this->httpHandlers[$httpStatusCode]))
        {
            $this->httpHandlers[$httpStatusCode] = new HttpHandler($httpStatusCode);
        }

        return $this->httpHandlers[$httpStatusCode];
    }
}