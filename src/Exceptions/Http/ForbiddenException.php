<?php
namespace Saq\Exceptions\Http;

use JetBrains\PhpStorm\Pure;

class ForbiddenException extends HttpException
{
    #[Pure]
    public function __construct()
    {
        parent::__construct('Forbidden', 403);
    }
}