<?php
namespace Saq\Exceptions\Http;

use JetBrains\PhpStorm\Pure;

class UnauthorizedException extends HttpException
{
    #[Pure]
    public function __construct()
    {
        parent::__construct('Unauthorized', 401);
    }
}