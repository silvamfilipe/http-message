<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Parser;

use Psr\Http\Message\RequestInterface;

/**
 * Parser factory that creates a parser object based on a request object
 *
 * This is a very basic factory that used te request headers to figure
 * out what is the best parser for that request. It is used by the
 * ServerRequest to set the parser if no parser is set before calling
 * ServerRequest::getParsedBody() method.
 *
 * @package Fsilva\HttpMessage\Parser
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ParserFactory
{
    /**
     * @var array An associative array where keys are content types and values
     *            are FQCN class names of the parser that will handel the
     *            parse.
     */
    private static $map = [
        'application/x-www-form-urlencoded' =>
            "Fsilva\\HttpMessage\\Parser\\UrlEncodedPost"
    ];

    /**
     * @var string The parser used if it is not possible to determine the
     *             correct parser to create
     */
    private static $defaultParser = "Fsilva\\HttpMessage\\Parser\\NullParser";

    /**
     * @var RequestInterface The request used to create the parser
     */
    private $request;

    /**
     * Creates a parser factory for provided request
     *
     * The constructor is private to force the usage of this class via
     * ParserFactory::getParserFor() method.
     *
     * @param RequestInterface $request The request used to create the parser
     */
    private function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Creates a parser for provided request
     *
     * @param RequestInterface $request The request used to create the parser
     *
     * @return ParserInterface The parser for provided request
     */
    public static function getParserFor(RequestInterface $request)
    {
        /** @var self $factory */
        $factory = new static($request);
        return $factory->create();
    }

    /**
     * Creates the parser for current request
     *
     * @return ParserInterface The parser for current request
     */
    private function create()
    {
        $header = $this->request->hasHeader('Content-Type')
            ? $this->request->getHeader('Content-Type')
            : null;

        $class = self::$defaultParser;
        if (isset(self::$map[$header])) {
            $class = self::$map[$header];
        }

        return new $class;
    }
}