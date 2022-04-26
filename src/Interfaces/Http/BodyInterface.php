<?php
namespace Saq\Interfaces\Http;

interface BodyInterface
{
    /**
     * @return string
     */
    function read(): string;

    /**
     * @return int
     */
    function getSize(): int;
}