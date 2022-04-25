<?php
namespace Saq\Routing\RouteArguments;

use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\Routing\RouteArgumentInterface;

class IntArg extends AbstractArgument implements RouteArgumentInterface
{
    /**
     * @var int|null
     */
    private ?int $min;

    /**
     * @var int|null
     */
    private ?int $max;

    /**
     * @param array $options
     */
    #[Pure]
    public function __construct(array $options)
    {
        $this->pattern = '-?\\d+';
        $this->min = array_key_exists('min', $options) ? (int)$options['min'] : null;
        $this->max = array_key_exists('max', $options) ? (int)$options['max'] : null;
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
     * @return int
     */
    public function filter(string $value): int
    {
        return (int)$value;
    }
}