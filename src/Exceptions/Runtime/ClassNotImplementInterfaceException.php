<?php
namespace Saq\Exceptions\Runtime;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class ClassNotImplementInterfaceException extends RuntimeException
{
    #[Pure]
    public function __construct(string $class, string $interface)
    {
        parent::__construct("Class \"{$class}\" does not implement interface \"{$interface}\".");
    }
}