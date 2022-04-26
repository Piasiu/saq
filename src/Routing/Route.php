<?php
namespace Saq\Routing;

use Attribute;
use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Routing\RouteArgumentInterface;

#[Attribute(\Attribute::TARGET_METHOD)]
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
     * @var string
     */
    private string $path;

    /**
     * @var array
     */
    private array $methods;

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
     * @var array|null
     */
    private ?array $rawCallable = null;

    /**
     * @param string $name
     * @param string $path
     * @param array|string[] $methods
     * @param array $arguments
     * @param array $defaults
     */
    #[Pure]
    public function __construct(string $name, string $path = '', array $methods = ['GET'], array $arguments = [], array $defaults = [])
    {
        $this->argumentResolver = new RouteArgumentResolver();
        $this->name = $name;
        $this->path = '/'.ltrim(trim($path), '/');
        $this->methods = $methods;
        $this->rawArguments = $arguments;
        $this->defaults = $defaults;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    #[Pure]
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return bool
     */
    #[Pure]
    public function hasArguments(): bool
    {
        return count($this->rawArguments) > 0;
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
            $this->pattern = $this->getPath();
            $arguments = $this->getArguments();

            foreach ($arguments as $name => $argument)
            {
                $pattern = "(?<{$name}>{$argument->getPattern()})";
                $this->pattern = str_replace('{'.$name.'}', $pattern, $this->pattern);
            }
        }

        return $this->pattern;
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
     * @param RouteGroup $group
     */
    public function addGroup(RouteGroup $group): void
    {
        $this->path = $group->getPath().$this->path;
        $this->rawArguments = array_merge($group->getRawArguments(), $this->rawArguments);
        $this->defaults = array_merge($group->getDefaults(), $this->defaults);
    }
}