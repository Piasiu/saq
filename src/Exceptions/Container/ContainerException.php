<?php
namespace Saq\Exceptions\Container;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class ContainerException extends RuntimeException
{
    #[Pure]
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}