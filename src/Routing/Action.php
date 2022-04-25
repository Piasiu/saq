<?php
namespace Saq\Routing;

use Saq\Interfaces\Routing\ActionInterface;

class Action implements ActionInterface
{
    /**
     * @var callable|null
     */
    private $callable = null;

    /**
     * @var array
     */
    private array $arguments = [];

    /**
     * @param callable $callable
     * @param array $arguments
     */
    public function set(callable $callable, array $arguments)
    {
        $this->callable = $callable;
        $this->arguments = $arguments;
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