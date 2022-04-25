<?php
namespace Saq\Routing;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Routing\RouteArgumentInterface;

abstract class Routable
{
    /**
     * @var RouteArgumentResolver|null
     */
    protected ?RouteArgumentResolver $argumentResolver = null;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var array
     */
    private array $rawArguments;

    /**
     * @var RouteArgumentInterface[]|null
     */
    protected ?array $arguments = null;

    /**
     * @var array
     */
    protected array $argumentsData = [];

    /**
     * @var array
     */
    private array $defaults;

    /**
     * @var string|null
     */
    protected ?string $pattern = null;

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
     * @return void
     */
    public function setArgumentResolver(RouteArgumentResolver $argumentResolver): void
    {
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return bool
     */
    #[Pure]
    public function hasArguments(): bool
    {
        return count($this->getRawArguments()) > 0;
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
        if ($this->argumentResolver === null)
        {
            $this->argumentResolver = new RouteArgumentResolver();
        }

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
}