<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Fsilva\HttpMessage\Exception\MissingHeaderException;
use Fsilva\HttpMessage\Exception\InvalidVersionException;
use Fsilva\HttpMessage\Exception\InvalidArgumentException;

/**
 * HTTP messages consist of requests from a client to a server and responses
 * from a server to a client. This interface defines the methods common to
 * each.
 *
 * Messages are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 *
 * @link http://www.ietf.org/rfc/rfc7230.txt
 * @link http://www.ietf.org/rfc/rfc7231.txt
 *
 * @package Fsilva\HttpMessage
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Message implements MessageInterface
{
    /**#@+
     * Protocol versions
     */
    const HTTP_1_0 = '1.0';
    const HTTP_1_1 = '1.1';
    const HTTP_2_0 = '2.0';
    /**#@- */

    /** @var array Accepted HTTP protocol version */
    private $validVersions = [self::HTTP_1_1, self::HTTP_1_0, self::HTTP_2_0];

    /** @var string HTTP protocol version, default to 1.1 */
    protected $protocolVersion = self::HTTP_1_1;

    /** @var string[]|array message's headers */
    protected $headers = [];

    /** @var StreamInterface */
    protected $body;

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Create a new instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return self
     */
    public function withProtocolVersion($version)
    {
        if (!in_array($version, $this->validVersions, true)) {
            throw new InvalidVersionException(
                "The provided version os not a valid HTTP protocol version."
            );
        }
        $message = clone($this);
        $message->protocolVersion = $version;
        return $message;
    }

    /**
     * Retrieves all message headers.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        $found = false;

        foreach ($this->headers as $headerName => $value) {
            if (strtolower($headerName) == strtolower($name)) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    /**
     * Retrieve a header by the given case-insensitive name, as a string.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeaderLines() instead
     * and supply your own delimiter when concatenating.
     *
     * @param string $name Case-insensitive header field name.
     * @return string
     */
    public function getHeader($name)
    {
        return implode(', ', $this->getHeaderLines($name));
    }

    /**
     * Retrieves a header by the given case-insensitive name as an array of strings.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[]
     */
    public function getHeaderLines($name)
    {
        $this->checkHeaderName($name);
        $value = [];
        foreach ($this->getHeaders() as $headerName => $value) {
            if (strtolower($headerName) == strtolower($name)) {
                break;
            }
        }
        return $value;
    }

    /**
     * Create a new instance with the provided header, replacing any existing
     * values of any headers with the same case-insensitive name.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        $value = $this->checkHeaderNameAndValue($name, $value);

        $message = clone($this);
        if ($message->hasHeader($name)) {
            $names = array_keys($message->headers);
            foreach ($names as $key) {
                if (strtolower($key) == strtolower($name)) {
                    unset($message->headers[$key]);
                    break;
                }
            }
        }
        $message->headers[$name] = $value;
        return $message;
    }

    /**
     * Creates a new instance, with the specified header appended with the
     * given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        $value = $this->checkHeaderNameAndValue($name, $value);

        $message = clone($this);
        $names = array_keys($message->headers);
        foreach($names as $key) {
            if (strtolower($key) == strtolower($name)) {
                $name = $key;
                break;
            }
        }
        foreach ($value as $val) {
            $message->headers[$name][] = $val;
        }
        return $message;
    }

    /**
     * Creates a new instance, without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name)
    {
        $message = clone($this);

        $names = array_keys($message->headers);
        foreach ($names as $key) {
            if (strtolower($key) == strtolower($name)) {
                unset($message->headers[$key]);
                break;

            }
        }

        return $message;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Create a new instance, with the specified message body.
     *
     * The body MUST be a StreamableInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return self
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        $message = clone($this);
        $message->body = $body;
        return $message;
    }

    /**
     * Checks if the provided name is a string and if an header with that
     * name exists in the message's headers.
     *
     * @see Message::getHeader()
     * @see Message::getHeaderLines()
     *
     * @param mixed $name
     */
    private function checkHeaderName($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(
                "The header name can only be a string"
            );
        }

        if (!$this->hasHeader($name)) {
            throw new MissingHeaderException(
                "The header your are trying to retrieve does not exists in " .
                "the HTTP message."
            );
        }
    }

    /**
     * Checks if the provided header name is a string and if the value is a
     * string or an array of strings.
     *
     * @see Message::withHeader()
     * @see Message::withAddedHeader()
     *
     * @param mixed $name
     * @param mixed $value
     *
     * @return string[]|array
     */
    private function checkHeaderNameAndValue($name, $value)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(
                "The header name can only be a string"
            );
        }

        if (!Validator::isValid('headerValue', $value)) {
            throw new InvalidArgumentException(
                "The header value for {$name} can only be a string or " .
                "array of strings."
            );
        }

        $value = is_string($value) ? [$value] : $value;

        return $value;
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name)
    {
        // TODO: Implement getHeaderLine() method.
    }
}
