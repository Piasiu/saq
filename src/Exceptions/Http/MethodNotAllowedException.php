<?php
namespace Saq\Exceptions\Http;

use JetBrains\PhpStorm\Pure;

class MethodNotAllowedException extends HttpException
{
    #[Pure]
    public function __construct()
    {
        parent::__construct('Method Not Allowed', 405);
    }
}