<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Fsilva\HttpMessage\Utils\ServerHeaders;
use Fsilva\HttpMessage\Parser\ParserFactory;
use Fsilva\HttpMessage\Utils\ServerRequestUri;
use Psr\Http\Message\ServerRequestInterface;
use Fsilva\HttpMessage\Parser\ParserInterface;
use Fsilva\HttpMessage\Exception\InvalidArgumentException;


/**
 * Representation of an incoming, server-side HTTP request.
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
 * Additionally, it encapsulates all data as it has arrived to the
 * application from the CGI and/or PHP environment, including:
 *
 * - The values represented in $_SERVER.
 * - Any cookies provided (generally via $_COOKIE)
 * - Query string arguments (generally via $_GET, or as parsed via parse_str())
 * - Upload files, if any (as represented by $_FILES)
 * - Deserialized body parameters (generally from $_POST)
 *
 * $_SERVER and $_FILES values are treated as immutable, as they represent
 * application state at the time of request; as such, no methods are provided
 * to allow modification of those values. The other values provide such methods,
 * as they can be restored from $_SERVER, $_FILES, or the request body, and may
 * need treatment during the application (e.g., body parameters may be
 * deserialized based on content type).
 *
 * Additionally, this implementation recognizes the utility of introspecting a
 * request to derive and match additional parameters (e.g., via URI path
 * matching, decrypting cookie values, deserializing non-form-encoded body
 * content, matching authorization headers to users, etc). These parameters
 * are stored in an "attributes" property.
 *
 * Requests are considered immutable; all methods that might change state are
 * implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 *
 * @package Fsilva\HttpMessage
 *
 */
class ServerRequest extends Request implements ServerRequestInterface
{

    /**
     * @var array Server parameters
     */
    private $serverParams;

    /**
     * @var array Cookie parameters
     */
    private $cookieParams;

    /**
     * @var array Query string parameters
     */
    private $queryParams;

    /**
     * @var array Uploaded file(s) meta data
     */
    private $files;

    /**
     * @var null|array|object
     */
    private $parsedData;

    /**
     * @var array attributes derived from the request
     */
    private $attributes = [];

    /**
     * @var ParserInterface The content parser for body parsing
     */
    private $contentParser;

    /**
     * Creates a request with CGI and/or PHP environment data
     */
    public function __construct()
    {
        $this->serverParams = $_SERVER;
        $this->cookieParams = $_COOKIE;
        $this->queryParams = $_GET;
        $this->files = $_FILES;
        $this->headers = ServerHeaders::get();

        if (isset($this->serverParams['REQUEST_METHOD'])) {
            $this->method = $this->serverParams['REQUEST_METHOD'];
        }

        $input = fopen('php://input', 'r');
        $temp = fopen('php://temp', 'r+');
        stream_copy_to_stream($input, $temp);

        $this->body = new Stream($temp);

        $this->parsedData =$this->getContentParser()
            ->setContent($this->body)
            ->parse();
        $uriParser = ServerRequestUri::parse($this);
        $this->uri = $uriParser->getUri();
        $this->target = $uriParser->getRequestUri();
    }

    /**
     * Returns ContentParser
     *
     * @return ParserInterface
     */
    public function getContentParser()
    {
        if (is_null($this->contentParser)) {
            $this->setContentParser(ParserFactory::getParserFor($this));
        }
        return $this->contentParser;
    }

    /**
     * Sets ContentParser
     *
     * @param ParserInterface $contentParser
     *
     * @returns self Returns a self instance useful on method chaining
     */
    public function setContentParser($contentParser)
    {
        $this->contentParser = $contentParser;
        return $this;
    }

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data id compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * Create a new instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method is implemented in such a way as to retain the
     * immutability of the message, and returns a new instance that has the
     * updated cookie values.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     *
     * @return self
     */
    public function withCookieParams(array $cookies)
    {
        if (!Validator::isValid('keyValueArray', $cookies)) {
            throw new InvalidArgumentException(
                "Trying to create a request with invalid cookie parameters"
            );
        }

        $request = clone($this);
        $request->cookieParams = $cookies;
        return $request;
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URL or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the composed URL or the `QUERY_STRING`
     * composed in the server params.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Create a new instance with the specified query string arguments.
     *
     * Setting query string arguments MUST NOT change the URL stored by the
     * request, nor the values in the server params.
     *
     * This method is implemented in such a way as to retain the
     * immutability of the message, and returns a new instance that has the
     * updated query string arguments.
     *
     * @param array $query Array of query string arguments, typically from
     *                     $_GET.
     *
     * @return self
     */
    public function withQueryParams(array $query)
    {
        if (!Validator::isValid('keyValueArray', $query)) {
            throw new InvalidArgumentException(
                "Trying to create a request with invalid query parameters"
            );
        }

        $request = clone($this);
        $request->queryParams = $query;
        return $request;
    }

    /**
     * Retrieve the upload file metadata.
     *
     * This method returns file upload metadata in the same structure
     * as PHP's $_FILES superglobal.
     *
     * @return array Upload file(s) metadata, if any.
     */
    public function getFileParams()
    {
        return $this->files;
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is application/x-www-form-urlencoded and the
     * request method is POST, this method will return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types are arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        return $this->parsedData;
    }

    /**
     * Create a new instance with the specified body parameters.
     *
     * If the request Content-Type is application/x-www-form-urlencoded and the
     * request method is POST, use this method ONLY to inject the contents of
     * $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method is implemented in such a way as to retain the
     * immutability of the message, and will return a new instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *                                typically be in an array or object.
     *
     * @throws InvalidArgumentException If the data is not an array, object or
     *                                  null value
     *
     * @return self
     */
    public function withParsedBody($data)
    {
        if (!is_null($data) && is_scalar($data)) {
            throw new InvalidArgumentException(
                "Request parsed data can ONLY be array, object on null value"
            );
        }
        $request = clone $this;
        $request->parsedData = $data;
        return $request;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @see getAttributes()
     *
     * @param string $name    The attribute name.
     * @param mixed  $default Default value to return if the attribute does not exist.
     *
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        $value = $default;
        if (isset($this->attributes[$name])) {
            $value = $this->attributes[$name];
        }
        return $value;
    }

    /**
     * Create a new instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method is implemented in such a way as to retain the immutability
     * of the message, and will return a new instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     *
     * @param string $name  The attribute name.
     * @param mixed  $value The value of the attribute.
     *
     * @return self
     */
    public function withAttribute($name, $value)
    {
        $request = clone $this;
        $request->attributes[$name] = $value;
        return $request;
    }

    /**
     * Create a new instance that removes the specified derived request
     * attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method is implemented in such a way as to retain the immutability
     * of the message, and will return a new instance that removes
     * the attribute.
     *
     * @see getAttributes()
     *
     * @param string $name The attribute name.
     *
     * @return self
     */
    public function withoutAttribute($name)
    {
        $request = clone $this;
        if (isset($request->attributes[$name])) {
            unset($request->attributes[$name]);
        }
        return $request;
    }
}
