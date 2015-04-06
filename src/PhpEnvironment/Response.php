<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\PhpEnvironment;

use Psr\Http\Message\ResponseInterface;
use Fsilva\HttpMessage\Response as PsrResponse;

/**
 * Response that extends the base PSR HTTP Response to provide methods to deal
 * with current PHP environment.
 *
 * @package Fsilva\HttpMessage\PhpEnvironment
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Response extends PsrResponse implements ResponseInterface
{

    /**
     * @var bool
     */
    private $headersSent = false;

    /**
     * @var bool
     */
    private $contentSent = false;

    /**
     * Check if headers have already been sent
     *
     * @return bool True if headers were sent, false otherwise
     */
    public function headersSent()
    {
        // Check any previous header sent
        if (!$this->headersSent) {
            $this->headersSent = headers_sent();
        }

        return $this->headersSent;
    }

    /**
     * Send HTTP headers
     *
     * @return Response
     */
    public function sendHeaders()
    {
        if ($this->headersSent()) {
            return $this;
        }

        header($this->renderStatusLine());

        foreach ($this->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        return $this;
    }

    /**
     * Send content
     *
     * @return Response
     */
    public function sendContent()
    {
        if (!$this->contentSent) {
            echo $this->getBody();
            $this->contentSent = true;
        }
        return $this;
    }

    /**
     * Send HTTP response
     *
     * @return Response
     */
    public function sent()
    {
        $this->sendHeaders()
            ->sendContent();
        return $this;
    }

    /**
     * Render the status line header
     *
     * @return string
     */
    public function renderStatusLine()
    {
        $status = sprintf(
            'HTTP/%s %d %s',
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase()
        );
        return trim($status);
    }
}