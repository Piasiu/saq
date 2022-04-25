<?php
namespace Saq\Exceptions\Http;

use JetBrains\PhpStorm\Pure;

class NotFoundException extends HttpException
{
    #[Pure]
    public function __construct()
    {
        parent::__construct('Not Found', 404);
    }
}