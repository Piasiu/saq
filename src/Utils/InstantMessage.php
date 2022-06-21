<?php
namespace Saq\Utils;

class InstantMessage
{
    /**
     * @var string
     */
    private string $message;

    /**
     * @var array
     */
    private array $attributes;

    /**
     * @param string $message
     * @param array $attributes
     */
    public function __construct(string $message, array $attributes = [])
    {
        $this->message = $message;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}