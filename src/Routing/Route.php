<?php
namespace Saq\Routing;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(\Attribute::TARGET_METHOD)]
class Route extends Routable
{
    /**
     * @var array
     */
    private array $methods;

    /**
     * @var array|null
     */
    private ?array $rawCallable = null;

    /**
     * @var RouteGroup|null
     */
    private ?RouteGroup $group = null;

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
        parent::__construct($path, $arguments, $defaults);
        $this->name = $name;
        $this->methods = $methods;
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
     * @return string
     */
    public function getPattern(): string
    {
        if ($this->pattern === null)
        {
            $this->pattern = $this->group !== null ? $this->group->getPattern() : '';
            $this->pattern .= $this->getPath();
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
    public function setGroup(RouteGroup $group): void
    {
        $this->group = $group;
    }
}