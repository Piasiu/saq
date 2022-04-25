<?php
namespace Saq\Exceptions\Runtime;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class ClassNotContainMethodException extends RuntimeException
{
    #[Pure]
    public function __construct(string $class, string $method)
    {
        parent::__construct("Class \"{$class}\" does not contain method \"{$method}\".");
    }
}