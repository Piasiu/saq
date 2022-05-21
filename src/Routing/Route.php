<?php
namespace Saq\Routing;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    /**
     * @var RouteArgumentResolver
     */
    protected RouteArgumentResolver $argumentResolver;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var array
     */
    private array $methods;

    /**
     * @var RouteSegment
     */
    private RouteSegment $lastSegment;

    /**
     * @var RouteSegment[]
     */
    private array $segments = [];

    /**
     * @var array|null
     */
    private ?array $rawCallable = null;

    /**
     * @var callable|null
     */
    private $callable = null;

    /**
     * @var array
     */
    private array $arguments = [];

    /**
     * @var Middleware[]
     */
    private array $middlewareList = [];

    /**
     * @var callable[]
     */
    private array $callableMiddlewareList = [];

    /**
     * @param string $name
     * @param string|RouteSegment $path
     * @param array|string[] $methods
     * @param array $arguments
     */
    public function __construct(string $name, string|RouteSegment $path = '', array $methods = ['GET'], array $arguments = [])
    {
        $this->argumentResolver = new RouteArgumentResolver();
        $this->name = $name;
        $this->methods = $methods;
        $this->lastSegment = $path instanceof RouteSegment ? $path : new RouteSegment($path, $arguments);
        $this->lastSegment->setArgumentResolver($this->argumentResolver);
        $this->lastSegment->setRoute($this);
        $this->segments[] = $this->lastSegment;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param RouteSegment $segment
     */
    public function addSegment(RouteSegment $segment): void
    {
        $segment->setArgumentResolver($this->argumentResolver);
        $segment->setRoute($this);
        $segment->setParent($this->lastSegment);
        $this->lastSegment = $segment;
        $this->segments[] = $segment;
    }

    /**
     * @return RouteSegment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    /**
     * @param array $rawCallable
     */
    public function setRawCallable(array $rawCallable): void
    {
        $this->rawCallable = $rawCallable;
    }

    /**
     * @return array|null
     */
    public function getRawCallable(): ?array
    {
        return $this->rawCallable;
    }

    /**
     * @param callable $callable
     */
    public function setCallable(callable $callable): void
    {
        $this->callable = $callable;
    }

    /**
     * @return callable|null
     */
    public function getCallable(): ?callable
    {
        return $this->callable;
    }

    /**
     * @return array
     */
    public function getAllArguments(): array
    {
        $arguments = [];

        foreach ($this->segments as $segment)
        {
            $arguments = array_merge($arguments, $segment->getArguments());
        }

        return $arguments;
    }

    /**
     * @return array
     */
    #[Pure]
    public function getAllDefaults(): array
    {
        $defaults = [];

        foreach ($this->segments as $segment)
        {
            $defaults = array_merge($defaults, $segment->getDefaults());
        }

        return $defaults;
    }

    /**
     * @param RoutePrefix $prefix
     */
    public function addPrefix(RoutePrefix $prefix): void
    {
        $segment = $this->segments[0];
        $segment->setPath($prefix->getPath().$segment->getPath());
        $segment->setRawArguments(array_merge($prefix->getRawArguments(), $segment->getRawArguments()));
    }

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param Middleware $middleware
     */
    public function addMiddleware(Middleware $middleware): void
    {
        $this->middlewareList[] = $middleware;
    }

    /**
     * @return Middleware[]
     */
    public function getMiddlewareList(): array
    {
        return $this->middlewareList;
    }

    /**
     * @param callable $callableMiddleware
     */
    public function addCallableMiddleware(callable $callableMiddleware): void
    {
        $this->callableMiddlewareList[] = $callableMiddleware;
    }

    /**
     * @return callable[]
     */
    public function getCallableMiddlewareList(): array
    {
        return $this->callableMiddlewareList;
    }
}