<?php
namespace Saq\Routing\RouteArguments;

use Saq\Exceptions\Runtime\OptionRequiredException;
use Saq\Interfaces\Routing\RouteArgumentInterface;

class RegexArg extends AbstractArgument implements RouteArgumentInterface
{
    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('pattern', $options))
        {
            throw new OptionRequiredException('pattern');
        }

        $this->pattern = $options['pattern'];
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