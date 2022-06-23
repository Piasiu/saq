<?php
namespace Saq\Utils;

use RuntimeException;

class DotEnv
{
    /**
     * @var string
     */
    private string $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath))
        {
            throw new RuntimeException(sprintf('File "%s" does not exist.', $filePath));
        }

        $this->filePath = $filePath;
    }

    public function load(): void
    {
        $data = parse_ini_file($this->filePath);

        if ($data === false)
        {
            throw new RuntimeException(sprintf('File "%s" is invalid.', $this->filePath));
        }

        foreach ($data as $name => $value)
        {
            putenv("{$name}={$value}");
        }
    }
}