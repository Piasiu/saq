<?php
namespace Saq\Routing;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(\Attribute::TARGET_CLASS)]
class RouteGroup
{
    /**
     * @var string
     */
    private string $path;

    /**
     * @var array
     */
    private array $rawArguments;

    /**
     * @var array
     */
    private array $defaults;

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
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getRawArguments(): array
    {
        return $this->rawArguments;
    }

    /**
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }
}