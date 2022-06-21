<?php
namespace Saq\Utils;

use ArrayAccess;
use JetBrains\PhpStorm\Pure;
use Stringable;

class InstantMessage implements Stringable, ArrayAccess
{
    /**
     * @var string
     */
    private string $content;

    /**
     * @var array
     */
    private array $attributes;

    /**
     * @param string $content
     * @param array $attributes
     */
    public function __construct(string $content, array $attributes = [])
    {
        $this->content = $content;
        $this->attributes = $attributes;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    #[Pure]
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ? $this->attributes[$offset] ? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }
}