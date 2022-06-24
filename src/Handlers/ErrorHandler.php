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

        $content = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/></head>';
        $content .= '<style>tbody tr:nth-child(odd) {background-color: #212121;} tbody tr:hover {background-color: #383838;} td, th {padding-left: 0.5rem; padding-right: 0.5rem;} th {text-align: left;} .file {color: #83ba52;} .line {color: #72e0d1; text-align: right;} .class {color: #fc9463;} .function {color: silver;} .arg {color: gray;}</style>';
        $content .= '<body style="background-color: #1b1b1b; color: #f5d67b;">';
        $content .= '<h1 style="color: #fc5433;">SAQ Error</h1>';
        $content .= "<p style=\"font-size: 1.4rem; color: white;\">{$throwable->getMessage()}</p>";
        $content .= '<table><thead><tr><th>#</th><th>Function</th><th>File</th><th>Line</th></tr></thead><tbody>';

        foreach ($traces as $i => $trace)
        {
            $no = $length - $i;
            $content .= "<tr><td>{$no}</td>";
            $content .= $this->getFunction($trace);

            if (isset($trace['file']) &&  isset($trace['line']))
            {
                $content .= $this->getFile($trace['file'], $trace['line']);
            }

            $content .= '</tr>';
        }

        $content .= '</tbody></table></body></html>';
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
        return "<td class=\"file\">{$file}</td><td class=\"line\">{$line}</td>";
    }

    /**
     * @param array $trace
     * @return string
     */
    private function getFunction(array $trace): string
    {
        if (isset($trace['class']))
        {
            $content = "<span class=\"class\">{$trace['class']}</span>::<span class=\"function\">{$trace['function']}</span>";
        }
        else
        {
            $content = "<span class=\"function\">{$trace['function']}</span>";
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

            $content .= "(<span class=\"arg\">".join("</span>, <span class=\"arg\">" , $args).'</span>)';
        }

        return "<td>{$content}</td>";
    }
}