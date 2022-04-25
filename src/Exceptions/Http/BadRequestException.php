<?php
namespace Saq\Exceptions\Http;

use JetBrains\PhpStorm\Pure;

class BadRequestException extends HttpException
{
    #[Pure]
    public function __construct()
    {
        parent::__construct('Bad Request', 400);
    }
}