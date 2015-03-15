<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Psr\Http\Message\UriInterface;
use Fsilva\HttpMessage\Exception\InvalidSchemeException;
use Fsilva\HttpMessage\Exception\InvalidArgumentException;
use Fsilva\HttpMessage\Exception\InvalidHostNameException;

/**
 * Value object representing a URI for use in HTTP requests.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state are implemented such that they retain the internal
 * state of the current instance and return a new instance that contains the
 * changed state.
 *
 * Typically the Host header will also be present in the request message.
 * For server-side requests, the scheme will typically be discoverable in the
 * server parameters.
 *
 * @link http://tools.ietf.org/html/rfc3986 (the URI specification)
 *
 * @package Fsilva\HttpMessage
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Uri implements UriInterface
{
    /**
     * @var string[] Allowed values for URI scheme
     */
    private $schemes = ['http', 'https', ''];

    /**
     * @var array Default scheme ports
     */
    private $defaultPorts = [
        'http' => '80',
        'https' => '443'
    ];

    /**
     * @var string URI scheme
     */
    private $scheme = '';

    /**
     * @var string User name
     */
    private $user = '';

    /**
     * @var string User password
     */
    private $pass;

    /**
     * @var string URI host name
     */
    private $host = '';

    /**
     * @var string|int Host port
     */
    private $port = '';

    /**
     * @var string Path segment of the URI
     */
    private $path = '';

    /**
     * @var string Query segment of the URI
     */
    private $query = '';

    /**
     * @var string The fragment of the URI
     */
    private $fragment = '';

    public function __construct($url = null)
    {
        $validUrl = Validator::isValid('url', $url);
        if (!is_null($url) && !$validUrl) {
            throw new InvalidArgumentException(
                "The URL '{$url}' is not valid."
            );
        }
        if (!is_null($url)) {
            $parts = parse_url($url);
            if ((bool) $parts && is_array($parts)) {
                foreach ($parts as $property => $value) {
                    $this->$property = $value;
                }
            }
        }
    }

    /**
     * Retrieve the URI scheme.
     *
     * If no scheme is present, this method returns an empty string.
     *
     * The string returned omits the trailing "://" delimiter if present.
     *
     * @return string The scheme of the URI.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority portion of the URI.
     *
     * The authority portion of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it will not be included.
     *
     * This method returns an empty string if no authority information is
     * present.
     *
     * @return string Authority portion of the URI, in "[user-info@]host[:port]"
     *     format.
     */
    public function getAuthority()
    {
        $authority = ($this->user !== '') ? $this->getUserInfo() .'@' : '';
        $port = '';
        if (!is_null($this->getPort())) {
            $port = ':'. $this->getPort();
        }
        return $authority.$this->getHost().$port;
    }

    /**
     * Retrieve the user information portion of the URI, if present.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * It will not return the "@" suffix when returning user information.
     *
     * @return string User information portion of the URI, if present, in
     *     "username[:password]" format.
     */
    public function getUserInfo()
    {
        $format = '%s%s';
        if (!is_null($this->pass) && $this->pass !== '') {
            $format = '%s:%s';
        }

        return sprintf($format, $this->user, $this->pass);
    }

    /**
     * Retrieve the host segment of the URI.
     *
     * This method returns a string; if no host segment is present, an
     * empty string in then returned.
     *
     * @return string Host segment of the URI.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port segment of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method returns it as an integer. If the port is the standard port
     * used with the current scheme, this method returns null.
     *
     * If no port is present this method returns a null value.
     *
     * @return null|int The port for the URI.
     */
    public function getPort()
    {
        $standard = ($this->scheme != '') ?
            $this->defaultPorts[$this->getScheme()] : 0;

        if ($this->port == '') {
            return null;
        }

        $port = intval($this->port);

        if ($this->port == $standard) {
            $port = null;
        }

        return $port;
    }

    /**
     * Retrieve the path segment of the URI.
     *
     * This method always return a string; if no path is present it will return
     * an empty string.
     *
     * @return string The path segment of the URI.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * This method will always return a string; if no query string is present,
     * it returns an empty string.
     *
     * The string returned omits the leading "?" character.
     *
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment segment of the URI.
     *
     * This method always returns a string; if no fragment is present, it will
     * return an empty string.
     *
     * The string returned will always omit the leading "#" character.
     *
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Create a new instance with the specified scheme.
     *
     * This method retains the state of the current instance, and return
     * a new instance that contains the specified scheme. If the scheme
     * provided includes the "://" delimiter, it will be removed.
     *
     * A scheme can only be one of the following values: "http", "https",
     * or an empty string.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     *
     * @return self A new instance with the specified scheme.
     * @throws \InvalidArgumentException|InvalidSchemeException for invalid or
     * unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $message = "Invalid or unsupported scheme. Supported schemes are " .
            "http, https or '' (empty string).";
        if (!is_string($scheme)) {
            throw new InvalidSchemeException($message);
        }
        $cleaned = strtolower(str_replace('://', '', $scheme));
        if (!in_array($cleaned, $this->schemes, true)) {
            throw new InvalidSchemeException($message);
        }
        $uri = clone($this);
        $uri->scheme = $cleaned;
        return $uri;
    }

    /**
     * Create a new instance with the specified user information.
     *
     * This method retains the state of the current instance, and return
     * a new instance that contains the specified user information.
     *
     * Password is optional, but the user information includes the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string      $user     User name to use for authority.
     * @param null|string $password Password associated with $user.
     *
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = clone($this);
        $uri->user = $user;
        $uri->pass = $password;
        return $uri;
    }

    /**
     * Create a new instance with the specified host.
     *
     * This method retains the state of the current instance, and returns
     * a new instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host Hostname to use with the new instance.
     *
     * @return self A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $valid = Validator::isValid('hotsname', $host);
        if (!$valid && $host != '') {
            throw new InvalidHostNameException(
                "The hostname '{$host}' ist not valid."
            );
        }

        $uri = clone($this);
        $uri->host = $host;
        return $uri;
    }

    /**
     * Create a new instance with the specified port.
     *
     * This method retains the state of the current instance, and return
     * a new instance that contains the specified port.
     *
     * An exception is raise for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port Port to use with the new instance; a null value
     *                       removes the port information.
     *
     * @return self A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $clearPort = ($port === '');
        $port = intval($port);
        $isDefault = in_array($port, $this->defaultPorts);
        $udpTcp = $port >= 1024 && $port <= 49151;

        if (!$isDefault && !$udpTcp && !$clearPort) {
            throw new InvalidArgumentException(
                "The port {$port} is not valid."
            );
        }

        if ($clearPort) {
            $port = '';
        }

        $uri = clone($this);
        $uri->port = $port;

        return $uri;
    }

    /**
     * Create a new instance with the specified path.
     *
     * This method retains the state of the current instance, and return
     * a new instance that contains the specified path.
     *
     * The path will be prefixed with "/" if not present
     *
     * An empty path value is equivalent to removing the path.
     *
     * @param string $path The path to use with the new instance.
     *
     * @return self A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        if (preg_match('/\s/', $path)) {
            throw new InvalidArgumentException(
                "The path '{$path}' contains invalid characters."
            );
        }

        $path = rtrim(str_replace('//', '/', ('/'. $path)), '/');

        $uri = clone($this);
        $uri->path = $path;
        return $uri;
    }

    /**
     * Create a new instance with the specified query string.
     *
     * This method retains the state of the current instance, and return
     * a new instance that contains the specified query string.
     *
     * If the query string is prefixed by "?", that character will be removed.
     * Additionally, the query string SHOULD be parseable by parse_str() in
     * order to be valid.
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     *
     * @return self A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException(
                'The query must be an URI formatted string.'
            );
        }
        $query = ltrim($query, '?');
        $uri = clone($this);
        $uri->query = $query;
        return $uri;
    }

    /**
     * Create a new instance with the specified URI fragment.
     *
     * This method retains the state of the current instance, and return
     * a new instance that contains the specified URI fragment.
     *
     * If the fragment is prefixed by "#", that character will be removed.
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The URI fragment to use with the new instance.
     *
     * @return self A new instance with the specified URI fragment.
     */
    public function withFragment($fragment)
    {
        $fragment = ltrim($fragment, '#');
        $uri = clone($this);
        $uri->fragment = $fragment;
        return $uri;
    }

    /**
     * Return the string representation of the URI.
     *
     * Concatenates the various segments of the URI, using the appropriate
     * delimiters:
     *
     * - If a scheme is present, "://" will append the value.
     * - If the authority information is present, that value will be
     *   concatenated.
     * - If a path is present, it will be prefixed by a "/" character.
     * - If a query string is present, it will be prefixed by a "?" character.
     * - If a URI fragment is present, it will be prefixed by a "#" character.
     *
     * @return string
     */
    public function __toString()
    {
        $schema = $this->getScheme();
        $str = ($schema != '') ? "{$schema}://" : $schema;
        $str .= $this->getAuthority();
        $str .= $this->getPath();
        $query = $this->getQuery();
        $str .= ($query != '') ? "?{$query}" : $query;
        $str .= ($this->fragment != '') ? "#{$this->fragment}" : $this->fragment;
        return $str;
    }
}
