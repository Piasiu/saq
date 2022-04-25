<?php
namespace Saq\Routing\RouteArguments;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Routing\RouteArgumentInterface;

class TextArg extends AbstractArgument implements RouteArgumentInterface
{
    /**
     * @param array $options
     */
    #[Pure]
    public function __construct(array $options)
    {
        $this->pattern = '[a-z]+';

        if (array_key_exists('length', $options))
        {
            $this->pattern = sprintf('[a-z]{%s}', (int)$options['length']);
        }
        else
        {
            $min = array_key_exists('min', $options) ? (int)$options['min'] : null;
            $max = array_key_exists('max', $options) ? (int)$options['max'] : null;

            if ($min !== null && $max !== null)
            {
                if ($max > $min)
                {
                    $this->pattern = sprintf('[a-z]{%s,%s}', $min, $max);
                }
            }
            elseif ($min !== null && $min > 1)
            {
                $this->pattern = sprintf('[a-z]{%s,}', $min);
            }
            elseif ($max !== null && $max > 1)
            {
                $this->pattern = sprintf('[a-z]{1,%s}', $max);
            }
        }
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