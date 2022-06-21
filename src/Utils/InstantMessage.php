<?php
namespace Saq\Utils;

use Stringable;

class InstantMessage implements Stringable
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
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : null;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->content;
    }
}