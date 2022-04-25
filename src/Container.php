<?php
namespace Saq;

use ArrayIterator;
use JetBrains\PhpStorm\Pure;
use ReflectionException;
use Saq\Exceptions\Container\ContainerException;
use Saq\Exceptions\Container\ServiceNotFoundException;
use Saq\Interfaces\CollectionInterface;
use Saq\Interfaces\ContainerInterface;
use Saq\Interfaces\Routing\CallableResolverInterface;
use Saq\Interfaces\Routing\RouterInterface;
use Saq\Interfaces\ServiceInterface;
use Saq\Routing\Router;

class Container implements ContainerInterface
{
    /**
     * @var ServiceInterface[]
     */
    private array $services = [];

    /**
     * @var CollectionInterface
     */
    private CollectionInterface $settings;

    /**
     * @var CallableResolver
     */
    private CallableResolver $callableResolver;

    /**
     * Container constructor.
     * @param array $settings
     */
    #[Pure]
    public function __construct(array $settings = [])
    {
        $this->callableResolver = new CallableResolver($this);
        $this->settings = new Collection($settings);
    }

    /**
     * @param string $id
     * @param object|callable|array|string $service
     * @return Container
     * @throws ReflectionException
     */
    public function set(string $id, object|callable|array|string $service): Container
    {
        if (is_object($service) && !is_callable($service))
        {
            $this->services[$id] = new ObjectService($service);
        }
        else
        {
            $callable = $this->callableResolver->resolve($service);
            $this->services[$id] = new FactoryService($callable, [$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * @inheritDoc
     * @throws ServiceNotFoundException
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id))
        {
            throw new ServiceNotFoundException($id);
        }

        return $this->services[$id]->build();
    }

    /**
     * @return CallableResolverInterface
     */
    public function getCallableResolver(): CallableResolverInterface
    {
        return $this->callableResolver;
    }

    /**
     * @inheritDoc
     */
    public function getSettings(): CollectionInterface
    {
        return $this->settings;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws ReflectionException
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    #[Pure]
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function offsetExists(mixed $offset): bool
    {
        return is_string($offset) && $this->has($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function offsetSet(mixed $offset, mixed $value)
    {
        if (!is_string($offset))
        {
            throw new ContainerException(sprintf('Container service name must be of type string, %s given.', gettype($offset)));
        }

        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset)
    {
        unset($this->services[$offset]);
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function count(): int
    {
        return count($this->services);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator();
    }
}