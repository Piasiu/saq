<?php
namespace Saq\Http;

use finfo;
use Saq\Interfaces\Http\UploadedFileInterface;

class UploadedFile implements UploadedFileInterface
{
    /**
     * @var string
     */
    private string $path;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $mime = '';

    /**
     * @var int
     */
    private int $size;

    /**
     * @var int
     */
    private int $error;

    /**
     * @param string $path
     * @param string $name
     * @param int $size
     * @param int $error
     */
    public function __construct(string $path, string $name, int $size, int $error)
    {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
        $this->error = $error;

        if ($error === UPLOAD_ERR_OK)
        {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $this->mime = $finfo->file($this->path);
        }
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getMime(): string
    {
        return $this->mime;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function moveTo(string $path): bool
    {
        return move_uploaded_file($this->path, $path);
    }
}