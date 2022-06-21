<?php
namespace Saq\Utils;

use ArrayAccess;
use JetBrains\PhpStorm\Pure;

class SessionManager implements ArrayAccess
{
    /**
     * @var string
     */
    private string $group;

    public function __construct(string $group)
    {
        $this->group = $group;

        if (!array_key_exists($group, $_SESSION))
        {
            $_SESSION[$group] = [];
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, mixed $value): void
    {
        $_SESSION[$this->group][$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    #[Pure]
    public function has(string $name): bool
    {
        return array_key_exists($name, $_SESSION[$this->group]);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, mixed $default = null): mixed
    {
        return $this->has($name) ? $_SESSION[$this->group][$name] : $default;
    }

    /**
     * @param string $name
     */
    public function remove(string $name): void
    {
        unset($_SESSION[$this->group][$name]);
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
        return is_string($offset) ? $this->get($offset) : null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_string($offset))
        {
            $this->set($offset, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        if (is_string($offset))
        {
            $this->remove($offset);
        }
    }
}