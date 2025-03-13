<?php
namespace Saq\Utils;

use Throwable;

class Logger
{
    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $defaultFileName;

    /**
     * @var string
     */
    private string $dateFormat;

    /**
     * @param string $path
     * @param string $defaultFileName
     * @param string $dateFormat
     */
    public function __construct(string $path, string $defaultFileName = 'app', string $dateFormat = 'Y-m-d H:i:s')
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
        $this->defaultFileName = $defaultFileName;
        $this->dateFormat = $dateFormat;

        if (!file_exists($this->path))
        {
            mkdir($this->path, 0777, true);
        }
    }

    /**
     * @param string $content
     * @param string $tag
     * @param string|null $fileName
     */
    public function write(string $content, string $tag, ?string $fileName = null): void
    {
        $fileName = $fileName ?? $this->defaultFileName;
        $content = date($this->dateFormat).' '.$tag.' '.$content."\r\n";
        file_put_contents($this->path.DIRECTORY_SEPARATOR.$fileName.'.log', $content, FILE_APPEND|LOCK_EX);
    }

    /**
     * @param string $content
     * @param string|null $fileName
     */
    public function info(string $content, ?string $fileName = null): void
    {
        $this->write($content, 'INFO', $fileName);
    }

    /**
     * @param string $content
     * @param string|null $fileName
     */
    public function error(string $content, ?string $fileName = null): void
    {
        $this->write($content, 'ERROR', $fileName);
    }

    /**
     * @param Throwable $throwable
     * @param string|null $fileName
     */
    public function exception(Throwable $throwable, ?string $fileName = null): void
    {
        $content = $throwable->getMessage();
        $traces = $throwable->getTrace();
        $length = count($traces);

        foreach ($traces as $i => $item)
        {
            $no = $length - $i;
            $content .= "\r\n#$no";

            if (isset($item['file']) && isset($item['line']))
            {
                $content .= " {$item['file']}:{$item['line']}";
            }
            elseif ($i === 0)
            {
                $content .= " {$throwable->getFile()}:{$throwable->getLine()}";
            }

            if (isset($item['class']))
            {
                $content .= " {$item['class']}::{$item['function']}";
            }
            else
            {
                $content .= " {$item['function']}";
            }
        }

        $this->error($content, $fileName);
    }
}