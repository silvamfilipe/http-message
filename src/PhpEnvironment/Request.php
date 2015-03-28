<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\PhpEnvironment;

use Fsilva\HttpMessage\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;


/**
 * This class extends ServerRequest to provide additional methods that help
 * dealing with request data.
 *
 * It is possible to retrieve a single server param, cookies, POST, query or
 * file value with default values if they are not defined. It is also possible
 * to determine the nature of the request (isPost, isGet, isDelete, ...)
 *
 * @package Fsilva\HttpMessage\PhpEnvironment
 */
class Request extends ServerRequest implements ServerRequestInterface
{
    /**
     * Checks if provided key exists in server params and returns it
     *
     * If the provided key is not defined the default value will be
     * returned instead
     *
     * @param string $key     The server param key name
     * @param mixed  $default The value that will be returned if the key is not
     *                        set in server params. Defaults to NULL.
     *
     * @return mixed The server value or the provided default value
     */
    public function getServer($key, $default = null)
    {
        $value = $default;
        $server = $this->getServerParams();
        if (isset($server[$key])) {
            $value = $server[$key];
        }
        return $value;
    }

    /**
     * Checks if a cookie with provided name exists and returns it
     *
     * if there is no cookie with the provided name the default value will
     * be returned instead
     *
     * @param string $name    The cookie name to retrieve
     * @param mixed  $default The value that will be returned if the cookie
     *                        does not exists. Defaults to NULL.
     *
     * @return mixed The cookie value or the provided default value
     */
    public function getCookie($name, $default = null)
    {
        $value = $default;
        $cookies = $this->getCookieParams();
        if (isset($cookies[$name])) {
            $value = $cookies[$name];
        }
        return $value;
    }

    /**
     * Check if a query parameter with provided name exists and returns it
     *
     * If there is no query parameter with provided name the default value
     * will be returned
     *
     * @param string $name    The query parameter name to retrieve
     * @param null   $default The value that will be returned if the query
     *                        parameter with provided name does not exists.
     *                        Defaults to Null
     *
     * @return mixed The query parameter or the provided default value
     */
    public function getQuery($name, $default = null)
    {
        $value = $default;
        $params = $this->getQueryParams();
        if (isset($params[$name])) {
            $value = $params[$name];
        }
        return $value;
    }

    /**
     * Checks if a files parameter with provided name exists and returns it
     *
     * If there is no files parameter with provided name the default value
     * will be returned
     *
     * @param string $name    The files parameter name to retrieve
     * @param array  $default The value that will be returned if the files
     *                        parameter with provided name does not exists.
     *                        Defaults to an empty array.
     *
     * @return array
     */
    public function getFiles($name, $default = [])
    {
        $value = $default;
        $files = $this->getFileParams();
        if (isset($files[$name])) {
            $value = $files[$name];
        }
        return $value;
    }

    /**
     * Check if a post parameter with provided name exists and returns it
     *
     * If there is no post parameter with provided name the default value
     * will be returned
     *
     * @param string $name    The post parameter name to retrieve
     * @param null   $default The value that will be returned if the post
     *                        parameter with provided name does not exists.
     *                        Defaults to Null
     *
     * @return mixed The post parameter or the provided default value
     */
    public function getPost($name, $default = null)
    {
        $value = $default;
        $post = $this->getParsedBody();
        if (isset($post[$name])) {
            $value = $post[$name];
        }
        return $value;
    }
}
