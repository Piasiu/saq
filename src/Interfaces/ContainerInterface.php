<?php
namespace Saq\Interfaces;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Routing\RouterInterface;

interface ContainerInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param string $id
     * @return bool
     */
    function has(string $id): bool;

    /**
     * @param string $id
     * @return mixed
     */
    function get(string $id): mixed;

    /**
     * @return CollectionInterface
     */
    function getSettings(): CollectionInterface;

    /**
     * @return RouterInterface
     */
    function getRouter(): RouterInterface;

    /**
     * @return RequestInterface
     */
    function getRequest(): RequestInterface;
}