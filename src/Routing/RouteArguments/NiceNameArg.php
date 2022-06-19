<?php
namespace Saq\Routing\RouteArguments;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Routing\RouteArgumentInterface;

class NiceNameArg extends AbstractArgument implements RouteArgumentInterface
{
    /**
     * @param array $options
     */
    #[Pure]
    public function __construct(array $options)
    {
        $this->pattern = '[a-z][\-a-z\d]*[a-z\d]';
    }

    /**
     * @param string $value
     * @return string
     */
    public function filter(string $value): string
    {
        return $value;
    }
}