<?php
namespace Saq\Http;

use Saq\Interfaces\Http\ResponseBodyInterface;

class ResponseBody implements ResponseBodyInterface
{
    /**
     * @var string
     */
    protected string $data;

    /**
     * @var int|null
     */
    protected ?int $size = null;

    /**
     * @param string $data
     */
    public function __construct(string $data = '')
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function write(string $data, bool $atBeginning = false): ResponseBody
    {
        if ($atBeginning)
        {
            $this->data = $data.$this->data;
        }
        else
        {
            $this->data .= $data;
        }

        $this->size = null;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function read(): string
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        if ($this->size === null)
        {
            $this->size = strlen($this->data);
        }

        return $this->size;
    }
}