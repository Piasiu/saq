<?php
namespace Saq\Interfaces\Http;

interface UploadedFileInterface
{
    /**
     * @return string
     */
    function getName(): string;

    /**
     * @return string
     */
    function getMime(): string;

    /**
     * @return int
     */
    function getSize(): int;

    /**
     * @return int
     */
    function getError(): int;

    /**
     * @param string $path
     * @return bool
     */
    function moveTo(string $path): bool;
}