<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\RequestInterface;
use Fsilva\HttpMessage\Exception\MissingHeaderException;
use Fsilva\HttpMessage\Exception\InvalidArgumentException;

/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this class includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * Requests are considered immutable; all methods that might change state are
 * implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 *
 * @package Fsilva\HttpMessage
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Request extends Message implements RequestInterface
{

    /**#@+
     * @var string HTTP request methods
     */
    const METHOD_OPTIONS  = 'OPTIONS';
    const METHOD_GET      = 'GET';
    const METHOD_HEAD     = 'HEAD';
    const METHOD_POST     = 'POST';
    const METHOD_PUT      = 'PUT';
    const METHOD_DELETE   = 'DELETE';
    const METHOD_TRACE    = 'TRACE';
    const METHOD_CONNECT  = 'CONNECT';
    const METHOD_PATCH    = 'PATCH';
    const METHOD_PROPFIND = 'PROPFIND';
    /**#@- */

    /**
     * @var string HTTP request method name
     */
    protected $method;

    /**
     * @var UriInterface Request URI
     */
    protected $uri;

    /**
     * @var string The request target
     */
    protected $target;

    /**
     * Extends MessageInterface::getHeaders() to provide request-specific
     * behavior.
     *
     * Retrieves all message headers.
     *
     * This method acts exactly like MessageInterface::getHeaders(), with one
     * behavioral change: if the Host header has not been previously set, the
     * method attempts to pull the host segment of the composed URI, if
     * present.
     *
     * @see MessageInterface::getHeaders()
     * @see UriInterface::getHost()
     * @return array Returns an associative array of the message's headers. Each
     *     key is a header name, and each value is an array of strings.
     */
    public function getHeaders()
    {
        $headers = parent::getHeaders();
        if (!$this->hasHeader('host')) {
            $host = $this->uri->getHost();
            if ($host != '') {
                $headers['host'] = [$host];
            }
        }
        return $headers;
    }

    /**
     * Extends MessageInterface::getHeader() to provide request-specific
     * behavior.
     *
     * This method acts exactly like MessageInterface::getHeader(), with
     * one behavioral change: if the Host header is requested, but has
     * not been previously set, the method attempts to pull the host
     * segment of the composed URI, if present.
     *
     * @see MessageInterface::getHeader()
     * @see UriInterface::getHost()
     * @param string $name Case-insensitive header field name.
     * @return string
     */
    public function getHeader($name)
    {
        return implode(',', $this->getHeaderLines($name));
    }

    /**
     * Extends MessageInterface::getHeaderLines() to provide request-specific
     * behavior.
     *
     * Retrieves a header by the given case-insensitive name as an array of strings.
     *
     * This method acts exactly like MessageInterface::getHeaderLines(), with
     * one behavioral change: if the Host header is requested, but has
     * not been previously set, the method MUST attempt to pull the host
     * segment of the composed URI, if present.
     *
     * @see MessageInterface::getHeaderLines()
     * @see UriInterface::getHost()
     * @param string $name Case-insensitive header field name.
     * @return string[]
     */
    public function getHeaderLines($name)
    {
        try {
            $header = parent::getHeaderLines($name);
        } catch (MissingHeaderException $exp) {
            $header = false;
        }

        if (strtolower($name) == 'host' && !$this->hasHeader($name)) {
            $host = (!is_null($this->uri)) ? $this->uri->getHost() : '';
            if ($host != '') {
                $header = [$host];
            }
        }

        if ($header === false) {
            throw new MissingHeaderException(
                'The header your are trying to retrieve does not exists in ' .
                'the HTTP request message.'
            );
        }
        return $header;
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method will return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        $target = $this->target;
        if (is_null($this->target)) {
            $target = $this->getTargetFromUri();
        }
        return $target;
    }

    /**
     * Create a new instance with a specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method is implemented in such a way as to retain the
     * immutability of the message, and returns a new instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        $request = clone($this);
        $request->target = $requestTarget;
        return $request;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Create a new instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method is implemented in such a way as to retain the
     * immutability of the message, and returns a new instance that has the
     * changed request method.
     *
     * @param string $method Case-insensitive method.
     * @return self|Request
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        if (!Validator::isValid('httpMethod', $method)) {
            throw new InvalidArgumentException(
                "Creating a request with invalid method."
            );
        }
        $request = clone($this);
        $request->method = $method;
        return $request;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method returns a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request, if any.
     */
    public function getUri()
    {
       return $this->uri;
    }

    /**
     * Create a new instance with the provided URI.
     *
     * This method is implemented in such a way as to retain the
     * immutability of the message, and returns a new instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @return self|Request
     */
    public function withUri(UriInterface $uri)
    {
        $request = clone($this);
        $request->uri = $uri;
        return $request;
    }

    /**
     * Retrieve the request target from the string
     *
     * @return string The path and query from the URI
     */
    private function getTargetFromUri()
    {
        $target = '/';
        if ($this->uri instanceof Uri) {
            $path = $this->uri->getPath();
            $target = $path == '' ? '/' : $path;
            $query = $this->uri->getQuery();
            $target .= ($query != '') ? "?{$query}" : $query;
        }
        return $target;
    }
}
