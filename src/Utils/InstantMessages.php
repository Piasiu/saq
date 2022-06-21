<?php
namespace Saq\Utils;

class InstantMessages
{
    const SESSION_KEY = 'instant-messages';

    /**
     * @var string[][]
     */
    protected array $messages = [];

    public function __invoke(): InstantMessages
    {
        if (array_key_exists(self::SESSION_KEY, $_SESSION) && is_array($_SESSION[self::SESSION_KEY]))
        {
            $this->messages = $_SESSION[self::SESSION_KEY];
        }

        $_SESSION[self::SESSION_KEY] = [];
        return $this;
    }

    /**
     * @param string $group
     * @param string $message
     */
    public function addLazyMessage(string $group, string $message): void
    {
        if (!array_key_exists($group, $_SESSION[self::SESSION_KEY]))
        {
            $_SESSION[self::SESSION_KEY][$group] = [];
        }

        $_SESSION[self::SESSION_KEY][$group][] = $message;
    }

    /**
     * @param string $group
     * @param string $message
     */
    public function addMessage(string $group, string $message): void
    {
        if (!array_key_exists($group, $this->messages))
        {
            $this->messages[$group] = [];
        }

        $this->messages[$group][] = $message;
    }

    /**
     * @return string[][]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}