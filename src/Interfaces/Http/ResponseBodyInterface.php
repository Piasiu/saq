<?php
namespace Saq\Interfaces\Http;

interface ResponseBodyInterface extends BodyInterface
{
    /**
     * @param string $data
     * @param bool $atBeginning
     * @return ResponseBodyInterface
     */
    function write(string $data, bool $atBeginning = false): ResponseBodyInterface;
}