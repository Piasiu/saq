<?php
namespace Saq\Routing;

use RuntimeException;
use Saq\Exceptions\Runtime\ClassNotExistException;
use Saq\Exceptions\Runtime\ClassNotImplementInterfaceException;
use Saq\Interfaces\Routing\RouteArgumentInterface;
use Saq\Routing\RouteArguments\RegexArg;

class RouteArgumentResolver
{
    /**
     * @param mixed $toResolve
     * @return RouteArgumentInterface
     */
    public function resolve(mixed $toResolve): RouteArgumentInterface
    {
        if ($toResolve instanceof RouteArgumentInterface)
        {
            return $toResolve;
        }

        if (is_array($toResolve))
        {
            list($class, $options) = $this->parseArray($toResolve);
        }
        elseif (is_string($toResolve))
        {
            list($class, $options) = $this->parseString($toResolve);
        }
        else
        {
            throw new RuntimeException("\"{$toResolve}\" is not resolvable.");
        }

        return $this->create($class, $options);
    }

    private function parseArray(array $toResolve): array
    {
        $length = count($toResolve);

        if ($length === 0)
        {
            throw new RuntimeException('Expected array with at least 1 element.');
        }

        if (count($toResolve) === 2)
        {
            return $toResolve;
        }

        return [$toResolve[0], []];
    }

    private function parseString(string $toResolve): array
    {
        if (class_exists($toResolve))
        {
            return [$toResolve, []];
        }

        return [RegexArg::class, ['pattern' => $toResolve]];
    }

    /**
     * @param string $class
     * @param array $options
     * @return RouteArgumentInterface
     */
    private function create(string $class, array $options): RouteArgumentInterface
    {
        if (!class_exists($class))
        {
            throw new ClassNotExistException($class);
        }

        $object = new $class($options);

        if (!($object instanceof RouteArgumentInterface))
        {
            throw new ClassNotImplementInterfaceException($class, RouteArgumentInterface::class);
        }

        return $object;
    }
}