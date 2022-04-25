<?php
namespace Saq;

use Saq\Interfaces\ServiceInterface;

class FactoryService implements ServiceInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private array $arguments;

    /**
     * @param callable $callable
     * @param array $arguments
     */
    public function __construct(callable $callable, array $arguments = [])
    {
        $this->callable = $callable;
        $this->arguments = $arguments;
    }

    /**
     * @inheritDoc
     */
    public function build(): mixed
    {
        return call_user_func_array($this->callable, $this->arguments);
    }
}