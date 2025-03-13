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
     * @inheritDoc
     */
    public function handle(RequestInterface $request, ResponseInterface $response, Throwable $throwable): ResponseInterface
    {
        $traces = $throwable->getTrace();
        $traces[0]['file'] = $throwable->getFile();
        $traces[0]['line'] = $throwable->getLine();
        $length = count($traces);

        if (in_array('application/json', $request->getHeader('Accept')))
        {
            $lines = [];

            foreach ($traces as $i => $trace)
            {
                $no = $length - $i;
                $line = [
                    'no' => $no,
                    'function' => $this->getFunction($trace),
                    'file' => '',
                ];

                if (isset($trace['file']) && isset($trace['line']))
                {
                    $line['file'] = $this->getFile($trace['file'], $trace['line']);
                }

                $lines[] = $line;
            }

            $response->withHeader('Content-Type', 'application/json');
            $content = json_encode([
                'message' => $throwable->getMessage(),
                'traces' => $lines
            ]);
        }
        else
        {
            $content = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/></head>';
            $content .= '<style>tbody tr:nth-child(odd) {background-color: #212121;} tbody tr:hover {background-color: #383838;} td, th {padding-left: 0.5rem; padding-right: 0.5rem;} th {text-align: left;} .file {color: #83ba52;} .line {color: #72e0d1; text-align: right;} .class {color: #fc9463;} .function {color: silver;} .arg {color: gray;}</style>';
            $content .= '<body style="background-color: #1b1b1b; color: #f5d67b;">';
            $content .= '<h1 style="color: #fc5433;">SAQ Error</h1>';
            $content .= "<p style=\"font-size: 1.4rem; color: white;\">{$throwable->getMessage()}</p>";
            $content .= '<table><thead><tr><th>#</th><th>Function</th><th>File</th><th>Line</th></tr></thead><tbody>';

            foreach ($traces as $i => $trace)
            {
                $no = $length - $i;
                $content .= "<tr><td>$no</td>";
                $content .= $this->getFunctionAsHTML($trace);

                if (isset($trace['file']) && isset($trace['line']))
                {
                    $content .= $this->getFileAsHTML($trace['file'], $trace['line']);
                }

                $content .= '</tr>';
            }

            $content .= '</tbody></table></body></html>';
        }

        $body = new ResponseBody($content);
        $response->withBody($body);
        return $response;
    }

    /**
     * @param string $file
     * @param string $line
     * @return string
     */
    private function getFileAsHTML(string $file, string $line): string
    {
        return "<td class=\"file\">$file</td><td class=\"line\">$line</td>";
    }

    /**
     * @param array $trace
     * @return string
     */
    private function getFunctionAsHTML(array $trace): string
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
            $content .= "(<span class=\"arg\">".join("</span>, <span class=\"arg\">" , $this->getFunctionArgs($trace['args'])).'</span>)';
        }

        return "<td>$content</td>";
    }

    /**
     * @param string $file
     * @param string $line
     * @return string
     */
    private function getFile(string $file, string $line): string
    {
        return "$file:$line";
    }

    /**
     * @param array $trace
     * @return string
     */
    private function getFunction(array $trace): string
    {
        if (isset($trace['class']))
        {
            $content = "{$trace['class']}::{$trace['function']}";
        }
        else
        {
            $content = $trace['function'];
        }

        if (isset($trace['args']))
        {
            $content .= '('.join(', ' , $this->getFunctionArgs($trace['args'])).')';
        }

        return $content;
    }

    private function getFunctionArgs(array $traceArgs): array
    {
        $args = [];

        foreach ($traceArgs as $arg)
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
        return $args;
    }
}