<?php
namespace Saq\Interfaces;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface CollectionInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param string $key
     * @param mixed $value
     * @return CollectionInterface
     */
    function set(string $key, mixed $value): CollectionInterface;

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get(string $key, mixed $default = null): mixed;

    /**
     * @param string $key
     * @return bool
     */
    function has(string $key): bool;

    /**
     * @param array $items
     * @return CollectionInterface
     */
    function replace(array $items): CollectionInterface;

    /**
     * @return array
     */
    function all(): array;

    /**
     * @param string $key
     * @return CollectionInterface
     */
    function remove(string $key): CollectionInterface;

    /**
     * @return CollectionInterface
     */
    function clear(): CollectionInterface;
}