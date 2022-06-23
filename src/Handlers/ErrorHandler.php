<?php
namespace Saq\Handlers;

use Saq\Http\ResponseBody;
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
        $traces = $throwable->getTrace();
        $traces[0]['file'] = $throwable->getFile();
        $traces[0]['line'] = $throwable->getLine();
        $length = count($traces);

        $content = "<!DOCTYPE html><html lang=\"en\"><head></head><meta charset=\"UTF-8\"/></head><body style=\"background-color: #1b1b1b; color: #f5d67b;\"><h1 style=\"color: #fc5433;\">SAQ Error</h1>";
        $content .= "<p style=\"font-size: 1.4rem; color: white;\">{$throwable->getMessage()}</p>";

        foreach ($traces as $i => $trace)
        {
            $no = $length - $i;
            $content .= "<p>#{$no} ";
            $content .= $this->getFile($trace['file'], $trace['line']);
            $content .= $this->getFunction($trace);
            $content .= '</p>';
        }

        $content .= '</body></html>';
        $body = new ResponseBody($content);
        $response->withBody($body);
        return $response;
    }

    /**
     * @param string $file
     * @param string $line
     * @return string
     */
    private function getFile(string $file, string $line): string
    {
        return "<span style=\"color: #83ba52;\">{$file}</span>:<span style=\"color: #72e0d1;\">{$line}</span>";
    }

    /**
     * @param array $trace
     * @return string
     */
    private function getFunction(array $trace): string
    {

        if (isset($trace['class']))
        {
            $content = " <span style=\"color: #fc9463\">{$trace['class']}</span>::<span style=\"color: silver\">{$trace['function']}</span>";
        }
        else
        {
            $content = " <span style=\"color: silver\">{$trace['function']}</span>";
        }

        if (isset($trace['args']))
        {
            $args = [];

            foreach ($trace['args'] as $arg)
            {
                if (is_string($arg))
                {
                    $args[] = $arg;
                }
                elseif (is_array($arg))
                {
                    $args[] = 'Array';
                }
                elseif (is_null($arg))
                {
                    $args[] = 'NULL';
                }
                elseif (is_bool($arg))
                {
                    $args[] = $arg ? 'true' : 'false';
                }
                elseif (is_object($arg))
                {
                    $args[] = get_class($arg);
                }
                elseif (is_resource($arg))
                {
                    $args[] = get_resource_type($arg);
                }
                else
                {
                    $args[] = $arg;
                }
            }

            $content .= "(<span style=\"color: gray\">".join("</span>, <span style=\"color: gray\">" , $args).'</span>)';
        }

        return $content;
    }
}