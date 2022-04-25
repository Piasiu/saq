<?php
namespace Saq\Routing\RouteArguments;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Routing\RouteArgumentInterface;

class FloatArg extends AbstractArgument implements RouteArgumentInterface
{
    /**
     * @var float|null
     */
    private ?float $min;

    /**
     * @var float|null
     */
    private ?float $max;

    /**
     * @param array $options
     */
    #[Pure]
    public function __construct(array $options)
    {
        $this->pattern = '-?\\d+(\\.\\d+)?';
        $this->min = array_key_exists('min', $options) ? (float)$options['min'] : null;
        $this->max = array_key_exists('max', $options) ? (float)$options['max'] : null;
    }

    /**
     * @inheritDoc
     */
    public function isValid(string $value): bool
    {
        if (!parent::isValid($value))
        {
            return false;
        }

        $value = $this->filter($value);

        if ($this->min !== null)
        {
            return $value >= $this->min;
        }

        if ($this->max !== null)
        {
            return $value <= $this->max;
        }

        return true;
    }

    /**
     * @param string $value
     * @return float
     */
    public function filter(string $value): float
    {
        return (float)$value;
    }
}