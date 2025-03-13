<?php
namespace Saq;

use ArrayIterator;
use JetBrains\PhpStorm\Pure;
use Saq\Interfaces\CollectionInterface;

class Collection implements CollectionInterface
{
    /**
     * @var array
     */
    private array $items;

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    function set(string $key, mixed $value): CollectionInterface
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    function get(string $key, mixed $default = null): mixed
    {
        return $this->has($key) ? $this->items[$key] : $default;
    }

    /**
     * @inheritDoc
     */
    function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * @inheritDoc
     */
    function replace(array $items): CollectionInterface
    {
        foreach ($items as $key => $value)
        {
            $this->items[$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    function remove(string $key): CollectionInterface
    {
        unset($this->items[$key]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    function clear(): CollectionInterface
    {
        $this->items = [];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
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
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}