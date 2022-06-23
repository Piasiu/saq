<?php
namespace Saq\Utils;

use RuntimeException;
use SplFileObject;

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
        $file = new SplFileObject($this->filePath);

        while (!$file->eof())
        {
            $line = $file->fgets();
            $env = $this->parseLine($line);

            if ($env !== null)
            {
                putenv(implode('=', $env));
                $_ENV[$env[0]] = $env[1];
            }
        }
    }

    /**
     * @param string $line
     * @return array|null
     */
    private function parseLine(string $line): ?array
    {
        $line = trim($line);

        if (strlen($line) > 0 && $line[0] !== '#')
        {
            $parts = explode('=', $line, 2);

            if (count($parts) === 2)
            {
                return [
                    $parts[0],
                    trim($parts[1], '"')
                ];
            }
        }

        return null;
    }
}