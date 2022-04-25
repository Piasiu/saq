<?php
namespace Saq\Routing\RouteArguments;

use Saq\Exceptions\Runtime\OptionRequiredException;
use Saq\Interfaces\Routing\RouteArgumentInterface;

class EnumArg extends AbstractArgument implements RouteArgumentInterface
{
    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('options', $options) || !is_array($options['options']) || count($options['options']) < 2)
        {
            throw new OptionRequiredException('options');
        }

        $this->pattern = implode('|', $options['options']);
        //$this->pattern = sprintf('(%s)', implode('|', $options['options']));
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