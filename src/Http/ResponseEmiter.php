<?php
namespace Saq\Http;

use Saq\Interfaces\Http\ResponseInterface;

class ResponseEmiter
{
    /**
     * @param ResponseInterface $response
     */
    public function emit(ResponseInterface $response): void
    {
        if (headers_sent() === false)
        {
            $this->emitStatusLine($response);
            $this->emitHeaders($response);
        }

        if ($response->getBody()->getSize() > 0)
        {
            echo $response->getBody()->read();
        }
    }

    /**
     * @param ResponseInterface $response
     */
    private function emitStatusLine(ResponseInterface $response): void
    {
        $statusLine = sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        header($statusLine, true, $response->getStatusCode());
    }

    /**
     * @param ResponseInterface $response
     */
    private function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values)
        {
            $replace = strtolower($name) !== 'set-cookie';

            foreach ($values as $value)
            {
                $header = sprintf('%s: %s', $name, $value);
                header($header, $replace);
                $replace = false;
            }
        }
    }
}