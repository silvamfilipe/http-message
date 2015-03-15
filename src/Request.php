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
    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_TRACE   = 'TRACE';
    /**#@- */

    /**
     * @var string HTTP request method name
     */
    private $method;

    /**
     * @var Uri Request URI
     */
    private $uri;

    /**
     * @var string The request target
     */
    private $target;

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
        $mode = 'root';
        $hasTarget = !is_null($this->target);
        $useUri = is_null($this->target) && $this->uri instanceof Uri;
        $mode = $hasTarget ? 'target' : $mode;
        $mode = $useUri ? 'uri': $mode;

        switch ($mode) {
            case 'target':
                $target = $this->target;
                break;

            case 'uri':
                $target = $this->getTargetFromUri();
                break;

            case 'root':
            default:
                $target = '/';
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
     * @return self
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
     * @return self
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
        $path = $this->uri->getPath();
        $target = $path == '' ? '/' : $path;
        $query = $this->uri->getQuery();
        $target .= ($query != '') ? "?{$query}" : $query;
        return $target;
    }
}
