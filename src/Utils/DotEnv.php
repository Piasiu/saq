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
        $fn = fopen($this->filePath, 'r');

        while (!feof($fn))
        {
            $line = fgets($fn, 20);
            $env = $this->parseLine($line);

            if ($env !== null)
            {
                putenv($env);
            }
        }

        fclose($fn);
    }

    /**
     * @param string $line
     * @return string|null
     */
    private function parseLine(string $line): ?string
    {
        $line = trim($line);

        if (strlen($line) > 0 && $line[0] !== '#')
        {
            $parts = explode('=', $line, 2);

            if (count($parts) === 2)
            {
                $name = $parts[0];
                $value = trim($parts[1], '"');
                return "$name=$value";
            }
        }

        return null;
    }
}