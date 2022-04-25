<?php
namespace Saq\Http;

use Saq\Interfaces\Http\RequestBodyInterface;

class RequestBody implements RequestBodyInterface
{
    /**
     * @var string|null
     */
    protected ?string $data = null;

    /**
     * @var array|null
     */
    private ?array $jsonData = null;

    /**
     * @var int|null
     */
    protected ?int $size = null;

    /**
     * @inheritDoc
     */
    public function read(): string
    {
        $this->prepareData();
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function readAsJson(): array
    {
        if ($this->jsonData === null)
        {
            $this->prepareData();
            $this->jsonData = json_decode($this->data, true);
        }

        return $this->jsonData;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        if ($this->size === null)
        {
            $this->prepareData();
            $this->size = strlen($this->data);
        }

        return $this->size;
    }

    private function prepareData(): void
    {
        if ($this->data === null)
        {
            $stream = fopen('php://input', 'r');
            rewind($stream);
            $data = stream_get_contents($stream);
            fclose($stream);
            $this->data = $data !== false ? $data : '';
        }
    }
}