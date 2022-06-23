<?php
namespace Saq\Utils;

class Log
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
    public function __construct(string $path, string $defaultFileName = 'app.log', string $dateFormat = 'Y-m-d H:i:s')
    {
        $this->path = realpath($path);
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
    public function write(string $content, string $tag = '', ?string $fileName = null): void
    {
        $fileName = $fileName ?? $this->defaultFileName;
        $content = date($this->dateFormat).' '.$tag."\r\n".$content."\r\n";
        file_put_contents($this->path.DIRECTORY_SEPARATOR.$fileName, $content, FILE_APPEND|LOCK_EX);
    }

    /**
     * @param string $content
     */
    public function info(string $content): void
    {
        $this->write($content, 'INFO');
    }

    /**
     * @param string $content
     */
    public function error(string $content): void
    {
        $this->write($content, 'ERROR');
    }
}