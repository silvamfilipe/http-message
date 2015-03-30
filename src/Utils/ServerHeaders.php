<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Utils;
use Fsilva\HttpMessage\ServerRequest;

/**
 * This is an utility class that parses and processes all request headers
 *
 * @package Fsilva\HttpMessage\Utils
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class ServerHeaders
{

    /**
     * @var array Server values from $_SERVER super global
     */
    private $server;

    /**
     * @var Callable The callable that retrieve the headers
     */
    private $headersCallback = 'apache_request_headers'; // Useful to mock some tests.

    /**
     * Initiates server parameters from $_SERVER
     */
    public function __construct(ServerRequest $request)
    {
        $this->server = $request->getServerParams();
        $this->normalizeServer();
    }

    /**
     * Sets headers retrieval callable
     *
     * @param callable $callable
     *
     * @return self A self instance for chain method calls
     */
    public function setHeadersCallback(Callable $callable)
    {
        $this->headersCallback = $callable;
        return $this;
    }

    /**
     * @return array
     */
    public static function get(ServerRequest $request)
    {
        $reader = new static($request);
        return $reader->getHeaders();
    }

    /**
     * Retrieve request headers
     *
     * @return array An associative array of request headers where keys
     *               are the header names and the values an array of all
     *               header values.
     */
    public function getHeaders()
    {
        $headers = array();
        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_COOKIE') === 0) {
                // Cookies are handled using the $_COOKIE super global
                continue;
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = strtr(substr($key, 5), '_', ' ');
                $name = strtr(ucwords(strtolower($name)), ' ', '-');
                $headers[$name] = $this->trimArray(explode(',', $value));
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = substr($key, 8); // Content-
                $name = 'Content-' . (($name == 'MD5') ?
                        $name : ucfirst(strtolower($name)));
                $headers[$name] = $this->trimArray(explode(',', $value));
                continue;
            }
        }
        return $headers;
    }

    /**
     * Pre-processes and returns the $_SERVER super global.
     */
    public function normalizeServer()
    {
        // This seems to be the only way to get the Authorization header
        // on Apache
        if (isset($this->server['HTTP_AUTHORIZATION'])
            || ! is_callable($this->headersCallback)
        ) {
            return;
        }

        $requestHeaders = call_user_func($this->headersCallback);
        if (isset($requestHeaders['Authorization'])) {
            $this->server['HTTP_AUTHORIZATION'] = $requestHeaders['Authorization'];
        } elseif (isset($requestHeaders['authorization'])) {
            $this->server['HTTP_AUTHORIZATION'] = $requestHeaders['authorization'];
        }
    }

    /**
     * Trims the elements of the provided array
     *
     * @param array $data The string array to trim
     *
     * @return array An array with its items trimmed
     */
    private function trimArray(array $data)
    {
        return array_map(
            function($item) {
                return trim($item);
            },
            $data
        );
    }
}
