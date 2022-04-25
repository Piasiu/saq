<?php
namespace Saq\Routing;

use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(\Attribute::TARGET_CLASS)]
class RouteGroup extends Routable
{
    /**
     * @return string
     */
    public function getPattern(): string
    {
        if ($this->pattern === null)
        {
            $this->pattern = $this->getPath();
            $arguments = $this->getArguments();

            foreach ($arguments as $name => $argument)
            {
                $this->pattern = str_replace('{'.$name.'}', $argument->getPattern(), $this->pattern);
            }
        }

        return $this->pattern;
    }
}