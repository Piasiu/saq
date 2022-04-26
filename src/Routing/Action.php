<?php
namespace Saq\Routing;

use Saq\Interfaces\Routing\ActionInterface;

class Action implements ActionInterface
{
    /**
     * @var string|null
     */
    private ?string $routeName = null;

    /**
     * @var callable|null
     */
    private $callable = null;

    /**
     * @var array
     */
    private array $arguments = [];

    /**
     * @param string $routeName
     * @param callable $callable
     * @param array $arguments
     */
    public function set(string $routeName, callable $callable, array $arguments)
    {
        $this->routeName = $routeName;
        $this->callable = $callable;
        $this->arguments = $arguments;
    }

    /**
     * @inheritDoc
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * @inheritDoc
     */
    public function getCallable(): ?callable
    {
        return $this->callable;
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool
    {
        return $this->callable !== null;
    }
}