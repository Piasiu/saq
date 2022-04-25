<?php
namespace Saq\Exceptions\Runtime;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class OptionRequiredException extends RuntimeException
{
    #[Pure]
    public function __construct(string $option, string $interface)
    {
        parent::__construct("Option \"{$option}\" is required.");
    }
}