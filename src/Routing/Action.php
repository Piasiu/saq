<?php
namespace Saq\Routing;

use Saq\Interfaces\Routing\ActionInterface;
use Saq\Interfaces\Routing\RouteInterface;

class Action implements ActionInterface
{
    /**
     * @var RouteInterface|null
     */
    private ?RouteInterface $route = null;

    /**
     * @var callable|null
     */
    private $callable = null;

    /**
     * @var array
     */
    private array $arguments = [];

    /**
     * @param RouteInterface $route
     * @param callable $callable
     * @param array $arguments
     */
    public function set(RouteInterface $route, callable $callable, array $arguments)
    {
        $this->route = $route;
        $this->callable = $callable;
        $this->arguments = $arguments;
    }

    /**
     * @inheritDoc
     */
    public function getRoute(): ?RouteInterface
    {
        return $this->route;
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