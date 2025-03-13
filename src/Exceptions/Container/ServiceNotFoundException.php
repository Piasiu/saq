<?php
namespace Saq\Exceptions\Container;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class ServiceNotFoundException extends RuntimeException
{
    #[Pure]
    public function __construct(string $message)
    {
        parent::__construct("Container service \"$message\" not found.");
    }
}