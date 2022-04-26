<?php
namespace Saq\Interfaces\Http;

interface RequestBodyInterface extends BodyInterface
{
    /**
     * @return array
     */
    function readAsJson(): array;
}