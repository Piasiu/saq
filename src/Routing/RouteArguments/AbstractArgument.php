<?php
namespace Saq\Routing\RouteArguments;

class AbstractArgument
{
    /**
     * @var string
     */
    protected string $pattern = '';

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isValid(string $value): bool
    {
        return preg_match('/^'.$this->pattern.'$/', $value) === 1;
    }
}