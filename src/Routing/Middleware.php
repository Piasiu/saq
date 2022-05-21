<?php
namespace Saq\Routing;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class Middleware
{
    /**
     * @var string
     */
    private string $class;

    /**
     * @var array
     */
    private array $rawCallable;

    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
        $this->rawCallable = [$class, '__invoke'];
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getRawCallable(): array
    {
        return $this->rawCallable;
    }
}