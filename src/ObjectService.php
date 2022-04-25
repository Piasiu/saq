<?php
namespace Saq;

use Saq\Interfaces\ServiceInterface;

class ObjectService implements ServiceInterface
{
    /**
     * @var object
     */
    private object $instance;

    /**
     * @param object $instance
     */
    public function __construct(object $instance)
    {
        $this->instance = $instance;
    }

    /**
     * @inheritDoc
     */
    public function build(): object
    {
        return $this->instance;
    }
}