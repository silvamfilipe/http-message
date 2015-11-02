<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Utils;

use Fsilva\HttpMessage\Uri;
use Fsilva\HttpMessage\ServerRequest;

/**
 * Utility class that detects the server request URI
 *
 * @package Fsilva\HttpMessage\Utils
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class ServerRequestUri
{

    /**
     * @var ServerRequest The request data used in parse
     */
    private $request;

    /**
     * Creates the parser/factory with a request dependency
     *
     * @param ServerRequest $request
     */
    private function __construct(ServerRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Crates an URI parser/factory for provided $request
     *
     * @param ServerRequest $request The request data used in parse
     *
     * @return ServerRequestUri
     */
    public static function parse(ServerRequest $request)
    {
        $factory = new static($request);
        return $factory;
    }

    /**
     * Detects and returns the full URI object
     *
     * @return Uri
     */
    public function getUri()
    {
        $scheme = $this->getServer('REQUEST_SCHEME', 'http');
        $port = $this->getServer('SERVER_PORT', 80);
        $serveName =  $this->getServer('SERVER_NAME');

        $uri = new Uri();

        return $uri->withScheme($scheme)
            ->withHost($serveName)
            ->withQuery($this->getServer('QUERY_STRING', ''))
            ->withPath($this->getBaseUrl())
            ->withPort($port);
    }

    /**
     * Returns the request URI tobe used as target in the request message
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->getServer('REQUEST_URI');
    }

    /**
     * Detects and returns the base URL and script name
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->detectBaseUrl();
    }

    /**
     * Detects and returns the request base path (without script name)
     *
     * @return mixed|string
     */
    public function getBasePath()
    {
        return $this->detectBasePath();
    }

    /**
     * Detects the request base URL
     *
     * Uses the server parameter values to determine the correct request URL
     *
     * @return string
     */
    protected function detectBaseUrl()
    {
        $filename = $this->request->getServerParams()['SCRIPT_FILENAME'];
        $phpSelf  = $this->request->getServerParams()['PHP_SELF'];
        $baseUrl  = '/';

        $basename = basename($filename);
        if ($basename) {
            $path = ($phpSelf ? trim($phpSelf, '/') : '');
            $basePos = strpos($path, $basename) ?: 0;
            $baseUrl .= substr($path, 0, $basePos) . $basename;
        }

        return$baseUrl;
    }

    /**
     * Detects the request base path
     *
     * Uses the server parameter values to determine the correct request path
     *
     * @return mixed|string
     */
    protected function detectBasePath()
    {
        $filename = basename(
            $this->request->getServerParams()['SCRIPT_FILENAME']
        );
        $baseUrl = $this->getBaseUrl();
        $basePath = $baseUrl;

        // basename() matches the script filename; return the directory
        if (basename($baseUrl) === $filename) {
            $basePath = str_replace('\\', '/', dirname($baseUrl));
        }
        return $basePath;
    }

    /**
     * Returns the server parameter with provided key or the default value
     *
     * The default value is used as the value that will be returned if the
     * server parameters has no value with $key name.
     *
     * @param string $key     The server parameter name to search
     * @param mixed  $default The default value returned if parameter does
     *                        not exists
     *
     * @return mixed The server parameter or the default value
     */
    protected function getServer($key, $default = null)
    {
        $server = $this->request->getServerParams();
        $value = $default;
        if (isset($server[$key])) {
            $value = $server[$key];
        }
        return $value;
    }
}