<?php
namespace Saq\Exceptions\Runtime;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class ClassNotExistException extends RuntimeException
{
    #[Pure]
    public function __construct(string $class)
    {
        parent::__construct("Class \"{$class}\" does not exist.");
    }
}