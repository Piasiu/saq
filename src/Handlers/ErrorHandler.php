<?php
namespace Saq\Handlers;

use Saq\Interfaces\Handlers\ErrorHandlerInterface;
use Saq\Interfaces\Http\RequestInterface;
use Saq\Interfaces\Http\ResponseInterface;
use Throwable;

class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var bool
     */
    private bool $displayDetails;

    /**
     * @param bool $displayDetails
     */
    public function __construct(bool $displayDetails = false)
    {
        $this->displayDetails = $displayDetails;
    }

    /**
     * @inheritDoc
     */
    public function handle(RequestInterface $request, ResponseInterface $response, Throwable $throwable): ResponseInterface
    {
        if ($this->displayDetails)
        {
            $template = '<h3>Error</h3><p>%s in <strong>%s</strong> on line <strong>%s</strong></p>';
            $data = sprintf($template, $throwable->getMessage(), $throwable->getFile(), $throwable->getLine());
            $items = $throwable->getTrace();
            $length = count($items);

            foreach ($items as $i => $item)
            {
                $data .= '<br>#'.($length - $i);

                if (isset($item['file']) && isset($item['line']))
                {
                    $data .= sprintf(' %s:%s', $item['file'], $item['line']);
                }

                if (isset($item['class']))
                {
                    $data .= sprintf(' %s::%s', $item['class'], $item['function']);
                }
                else
                {
                    $data .= ' '.$item['function'];
                }
            }
        }
        else
        {
            $data = sprintf('<h3>Error</h3><p>%s</p>', $throwable->getMessage());
        }

        $response->getBody()->write($data);
        return $response;
    }
}