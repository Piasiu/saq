<?php
namespace Saq\Routing\RouteArguments;

use JetBrains\PhpStorm\Pure;

class IdArg extends IntArg
{
    /**
     * @param array $options
     */
    #[Pure]
    public function __construct(array $options)
    {
        $options['min'] = 1;
        parent::__construct($options);
    }
}