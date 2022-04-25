<?php
namespace Saq;

use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Saq\Exceptions\Runtime\ClassNotContainMethodException;
use Saq\Exceptions\Runtime\ClassNotExistException;
use Saq\Interfaces\ContainerInterface;
use Saq\Interfaces\Routing\CallableResolverInterface;

class CallableResolver implements CallableResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    function resolve(callable|array|string $toResolve): callable
    {
        if (is_callable($toResolve))
        {
            return $toResolve;
        }

        if (is_array($toResolve))
        {
            list($class, $method) = $this->parseArray($toResolve);
        }
        elseif (is_string($toResolve))
        {
            list($class, $method) = $this->parseString($toResolve);
        }
        else
        {
            throw new RuntimeException("\"{$toResolve}\" is not resolvable.");
        }

        return $this->create($class, $method);
    }

    /**
     * @param array $toResolve
     * @return array
     */
    private function parseArray(array $toResolve): array
    {
        if (count($toResolve) === 2)
        {
            return $toResolve;
        }

        return [$toResolve[0], '__invoke'];
    }

    /**
     * @param string $toResolve
     * @return array
     */
    #[Pure]
    private function parseString(string $toResolve): array
    {
        $parts = explode(':', $toResolve);

        if (count($parts) === 2)
        {
            return $parts;
        }

        return [$toResolve, '_invoke'];
    }

    /**
     * @param string $class
     * @param string $method
     * @return callable
     * @throws ReflectionException
     * @throws RuntimeException
     */
    private function create(string $class, string $method): callable
    {
        if (!class_exists($class))
        {
            throw new ClassNotExistException($class);
        }

        $reflection = new ReflectionClass($class);

        if (!$reflection->hasMethod($method))
        {
            throw new ClassNotContainMethodException($class, $method);
        }

        if ($this->container->has($class))
        {
            $object = $this->container->get($class);
        }
        else
        {
            $object = new $class($this->container);
            $this->container->set($class, $object);
        }

        return [$object, $method];
    }
}