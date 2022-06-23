<?php
namespace Saq\Utils;

class Log
{
    /**
     * @var string
     */
    private string $filePath;

    /**
     * @var string
     */
    private string $dateFormat;

    /**
     * @param string $filePath
     * @param string $dateFormat
     */
    public function __construct(string $filePath, string $dateFormat = 'Y-m-d H:i:s')
    {
        $this->filePath = $filePath;
        $this->dateFormat = $filePath;
    }

    /**
     * @param string $content
     * @param string $tag
     */
    public function write(string $content, string $tag = ''): void
    {
        $content = date($this->dateFormat).' '.$tag."\r\n".$content."\r\n";
        file_put_contents($this->filePath, $content, FILE_APPEND|LOCK_EX);
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