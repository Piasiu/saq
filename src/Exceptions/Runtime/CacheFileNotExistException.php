<?php
namespace Saq\Exceptions\Runtime;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class CacheFileNotExistException extends RuntimeException
{
    #[Pure]
    public function __construct(string $path)
    {
        parent::__construct("Cache file \"$path\" does not exist.");
    }
}