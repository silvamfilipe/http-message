<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\PhpEnvironment;

use Fsilva\HttpMessage\ServerRequest;
use Fsilva\HttpMessage\Utils\ServerRequestUri;
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
 *
 * @method mixed|string getServer(string $key, mixed $default = null) Checks if
 *   provided key exists in server params and returns it.
 * @method mixed|string getPost(string $name, mixed $default = null) Check if a
 *   post parameter with provided name exists and returns it.
 * @method mixed|string getCookie(string $name, mixed $default = null) Checks
 *   if a cookie with provided name exists and returns it.
 * @method mixed|string getQuery(string $name, mixed $default = null) Check if
 *   a query parameter with provided name exists and returns it.
 * @method mixed|
 */
class Request extends ServerRequest implements ServerRequestInterface
{

    /**
     * @var string The request base path
     */
    private $basePath;

    /**
     * @var array The callbacks used to retrieve request values
     */
    private $callbacks = [
        'getServer' => 'getServerParams',
        'getQuery' => 'getQueryParams',
        'getPost' => 'getParsedBody',
        'getCookie' => 'getCookieParams',
    ];

    /**
     * Check if the calling method is one of the callbacks and call the common
     * method for request parameters retrieval.
     *
     * @see getValue()
     *
     * @param string $name      Method name
     * @param array  $arguments Arguments passed along with method call
     *
     * @return mixed
     *
     * @throws \BadMethodCallException If the method is not defined
     */
    public function __call($name, $arguments)
    {
        if (isset($this->callbacks[$name])) {
            $method = $this->callbacks[$name];
            $values = $this->$method();
            array_unshift($arguments, $values);
            return call_user_func_array([$this, 'getValue'], $arguments);
        }

        $class = __CLASS__;
        throw new \BadMethodCallException(
            "{$name} method is not defined in {$class}"
        );
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
     * Returns the request URI base path
     *
     * @return mixed|string
     */
    public function getBasePath()
    {
        if (is_null($this->basePath)) {
            $factory = ServerRequestUri::parse($this);
            $this->basePath = $factory->getBasePath();
        }
        return $this->basePath;
    }

    /**
     * General propose method that searches the provided array of values to
     * find the given name returning its value of the default value if not
     * found.
     *
     * @param array  $values  The array to search
     * @param string $name    The key name to find out
     * @param mixed  $default The default value it key does not exists
     *
     * @return mixed The value for the given key name or the default value it
     *  the key does not exists
     */
    protected function getValue($values, $name, $default = null)
    {
        $value = $default;
        if (isset($values[$name])) {
            $value = $values[$name];
        }
        return $value;
    }
}
