<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Fsilva\HttpMessage\Exception\InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Fsilva\HttpMessage\Exception\InvalidHostNameException;
use Fsilva\HttpMessage\Exception\InvalidSchemeException;

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
    private $password = '';

    /**
     * @var string URI host name
     */
    private $host = '';

    /**
     * @var string Host port
     */
    private $port = '';

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
     * scheme, it SHOULD NOT be included.
     *
     * This method returns an empty string if no authority information is
     * present.
     *
     * @return string Authority portion of the URI, in "[user-info@]host[:port]"
     *     format.
     */
    public function getAuthority()
    {
        // TODO: Implement getAuthority() method.
    }

    /**
     * Retrieve the user information portion of the URI, if present.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * Implementations MUST NOT return the "@" suffix when returning this value.
     *
     * @return string User information portion of the URI, if present, in
     *     "username[:password]" format.
     */
    public function getUserInfo()
    {
        // TODO: Implement getUserInfo() method.
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
        $port = null;
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
     * This method MUST return a string; if no path is present it MUST return
     * an empty string.
     *
     * @return string The path segment of the URI.
     */
    public function getPath()
    {
        // TODO: Implement getPath() method.
    }

    /**
     * Retrieve the query string of the URI.
     *
     * This method MUST return a string; if no query string is present, it MUST
     * return an empty string.
     *
     * The string returned MUST omit the leading "?" character.
     *
     * @return string The URI query string.
     */
    public function getQuery()
    {
        // TODO: Implement getQuery() method.
    }

    /**
     * Retrieve the fragment segment of the URI.
     *
     * This method MUST return a string; if no fragment is present, it MUST
     * return an empty string.
     *
     * The string returned MUST omit the leading "#" character.
     *
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        // TODO: Implement getFragment() method.
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
        $scheme = strtolower(str_replace('://', '', $scheme));
        if (!in_array($scheme, $this->schemes, true)) {
            throw new InvalidSchemeException($message);
        }
        $uri = clone($this);
        $uri->scheme = $scheme;
        return $uri;
    }

    /**
     * Create a new instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
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
        // TODO: Implement withUserInfo() method.
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
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified path.
     *
     * The path MUST be prefixed with "/"; if not, the implementation MAY
     * provide the prefix itself.
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
        // TODO: Implement withPath() method.
    }

    /**
     * Create a new instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified query string.
     *
     * If the query string is prefixed by "?", that character MUST be removed.
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
        // TODO: Implement withQuery() method.
    }

    /**
     * Create a new instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * a new instance that contains the specified URI fragment.
     *
     * If the fragment is prefixed by "#", that character MUST be removed.
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The URI fragment to use with the new instance.
     *
     * @return self A new instance with the specified URI fragment.
     */
    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    /**
     * Return the string representation of the URI.
     *
     * Concatenates the various segments of the URI, using the appropriate
     * delimiters:
     *
     * - If a scheme is present, "://" MUST append the value.
     * - If the authority information is present, that value will be
     *   concatenated.
     * - If a path is present, it MUST be prefixed by a "/" character.
     * - If a query string is present, it MUST be prefixed by a "?" character.
     * - If a URI fragment is present, it MUST be prefixed by a "#" character.
     *
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
}}