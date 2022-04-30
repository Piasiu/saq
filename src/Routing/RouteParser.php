<?php
namespace Saq\Routing;

use RuntimeException;

class RouteParser
{
    /**
     * @param Route $route
     * @param array $arguments
     * @return string
     */
    public function urlFor(Route $route, array $arguments = []): string
    {
        $allSegments = $route->getSegments();
        $segments[] = array_shift($allSegments);
        $segments = array_merge($segments, $this->getRequiredSegments($allSegments, $arguments));
        $url = '';

        foreach ($segments as $segment)
        {
            $defaults = $segment->getDefaults();
            $path = $segment->getPath();

            foreach ($segment->getArguments() as $name => $routeArgument)
            {
                $value = null;

                if (array_key_exists($name, $arguments))
                {
                    $value = $arguments[$name];
                }
                elseif (array_key_exists($name, $defaults))
                {
                    $value = $defaults[$name];
                }

                if (!isset($value))
                {
                    throw new RuntimeException("Argument \"{$name}\" of route \"{$route->getName()}\" is missing.");
                }

                if (!$routeArgument->isValid($value))
                {
                    throw new RuntimeException("Given value \"{$value}\" for argument \"{$name}\" of route \"{$route->getName()}\" is invalid.");
                }

                $path = str_replace('{'.$name.'}', $value, $path);
            }

            $url .= $path;
        }

        return $url;
    }

    /**
     * @param RouteSegment[] $segments
     * @param array $arguments
     * @return RouteSegment[]
     */
    private function getRequiredSegments(array $segments, array $arguments): array
    {
        foreach (array_reverse($segments) as $index => $segment)
        {
            $names = array_keys($segment->getArguments());

            foreach ($names as $name)
            {
                if (array_key_exists($name, $arguments))
                {
                    $length = count($segments) - $index;
                    $result = [];

                    for ($index = 0; $index < $length; $index++)
                    {
                        $result[] = $segments[$index];
                    }

                    return $result;
                }
            }
        }

        return [];
    }
}