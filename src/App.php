<?php
namespace Saq;

use JetBrains\PhpStorm\Pure;
use RuntimeException;
use Saq\Exceptions\Http\HttpException;
use Saq\Exceptions\Http\NotFoundException;
use Saq\Handlers\ErrorHandler;
use Saq\Handlers\HttpHandler;
use Saq\Handlers\NotFoundHandler;
use Saq\Http\Request;
use Saq\Http\Response;
use Saq\Http\ResponseEmiter;
use Saq\Interfaces\ContainerInterface;
use Saq\Interfaces\Handlers\ErrorHandlerInterface;
use Saq\Interfaces\Handlers\HttpHandlerInterface;
use Saq\Interfaces\Handlers\NotFoundHandlerInterface;
use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\ResponseInterface;
use Saq\Interfaces\Routing\RouterInterface;
use Saq\Routing\Router;
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
     * @param ContainerInterface|array $container
     */
    #[Pure]
    public function __construct(ContainerInterface|array $container = [])
    {
        $this->container = is_array($container) ? new Container($container) : $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
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
        $router = $this->container->getRouter();
        $action = $router->handle($request->getMethod(), $request->getUri());

        if (!$action->exists())
        {
            throw new NotFoundException();
        }

        // TODO Obsługa middelware-ów.
        $result = call_user_func_array($action->getCallable(), [$request, $response, $action->getArguments()]);

        if ($result instanceof ResponseInterface)
        {
            $response = $result;
        }

        return $response;
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