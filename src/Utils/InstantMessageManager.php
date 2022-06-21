<?php
namespace Saq\Utils;

class InstantMessageManager
{
    const SESSION_KEY = 'instant-messages';

    /**
     * @var InstantMessage[]
     */
    protected array $messages = [];

    public function __invoke(): InstantMessageManager
    {
        if (array_key_exists(self::SESSION_KEY, $_SESSION) && is_array($_SESSION[self::SESSION_KEY]))
        {
            $this->messages = $_SESSION[self::SESSION_KEY];
        }

        $_SESSION[self::SESSION_KEY] = [];
        return $this;
    }

    /**
     * @param InstantMessage $message
     */
    public function addLazyMessage(InstantMessage $message): void
    {
        $_SESSION[self::SESSION_KEY][] = $message;
    }

    /**
     * @param InstantMessage $message
     */
    public function addMessage(InstantMessage $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return InstantMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}