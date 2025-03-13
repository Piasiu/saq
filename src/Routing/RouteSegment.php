<?php
namespace Saq\Routing;

use Attribute;
use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Routing\RouteArgumentInterface;

#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class RouteSegment
{
    /**
     * @var RouteArgumentResolver|null
     */
    private ?RouteArgumentResolver $argumentResolver;

    /**
     * @var Route|null
     */
    private ?Route $route = null;

    /**
     * @var RouteSegment|null
     */
    private ?RouteSegment $parent = null;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var array
     */
    protected array $rawArguments;

    /**
     * @var RouteArgumentInterface[]|null
     */
    private ?array $arguments = null;

    /**
     * @var array
     */
    private array $defaults;

    /**
     * @var string|null
     */
    private ?string $pattern = null;

    /**
     * @param string $path
     * @param array $arguments
     * @param array $defaults
     */
    #[Pure]
    public function __construct(string $path, array $arguments = [], array $defaults = [])
    {
        $this->path = '/'.ltrim(trim($path), '/');
        $this->rawArguments = $arguments;
        $this->defaults = $defaults;
    }

    /**
     * @param RouteArgumentResolver $argumentResolver
     */
    public function setArgumentResolver(RouteArgumentResolver $argumentResolver): void
    {
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * @param Route $route
     */
    public function setRoute(Route $route): void
    {
        $this->route = $route;
    }

    /**
     * @return Route|null
     */
    public function getRoute(): ?Route
    {
        return $this->route;
    }

    /**
     * @param RouteSegment $segment
     * @return void
     */
    public function setParent(RouteSegment $segment): void
    {
        $this->parent = $segment;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param array $arguments
     */
    public function setRawArguments(array $arguments): void
    {
        $this->rawArguments = $arguments;
        $this->arguments = null;
    }

    /**
     * @return array
     */
    public function getRawArguments(): array
    {
        return $this->rawArguments;
    }

    /**
     * @return RouteArgumentInterface[]
     */
    public function getArguments(): array
    {
        if ($this->arguments === null)
        {
            $this->arguments = [];

            foreach ($this->rawArguments as $name => $data)
            {
                $argument = $this->argumentResolver->resolve($data);
                $this->arguments[$name] = $argument;
            }
        }

        return $this->arguments;
    }

    /**
     * @return array
     */
    public function getAllArguments(): array
    {
        $arguments = $this->parent !== null ? $this->parent->getAllArguments() : [];
        return array_merge($arguments, $this->getArguments());
    }

    /**
     * @return bool
     */
    #[Pure]
    public function hasArguments(): bool
    {
        if ($this->parent !== null && $this->parent->hasArguments())
        {
            return true;
        }

        return count($this->rawArguments) > 0;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        if ($this->pattern === null)
        {
            $this->pattern = $this->path;
            $arguments = $this->getArguments();

            foreach ($arguments as $name => $argument)
            {
                $pattern = "(?<$name>{$argument->getPattern()})";
                $this->pattern = str_replace('{'.$name.'}', $pattern, $this->pattern);
            }
        }

        return $this->pattern;
    }

    /**
     * @return string
     */
    public function getFullPattern(): string
    {
        $pattern = $this->parent !== null ? $this->parent->getFullPattern() : '';
        return $pattern.$this->getPattern();
    }
}