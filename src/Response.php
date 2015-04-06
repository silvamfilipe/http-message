<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Psr\Http\Message\ResponseInterface;
use Fsilva\HttpMessage\Exception\InvalidArgumentException;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state are
 * implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 *
 * @package Fsilva\HttpMessage
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Response extends Message implements ResponseInterface
{
    /**
     * @var array Recommended Reason Phrases
     */
    private static $recommendedReasonPhrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var int The status code
     */
    protected $statusCode = 200;

    /**
     * @var string The status reason phrase
     */
    protected $reasonPhrase = 'OK';

    /**
     * Gets the response Status-Code.
     *
     * The Status-Code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return integer Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Create a new instance with the specified status code, and optionally
     * reason phrase, for the response.
     *
     * If no Reason-Phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * Status-Code.
     *
     * This method is implemented in such a way as to retain the
     * immutability of the message, and will return a new instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     *
     * @param integer $code The 3-digit integer result code to set.
     * @param null|string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, the recommended phrase
     *     will be set.
     *
     * @return self
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = null)
    {
        if (!Validator::isValid('statusCode', $code)) {
            throw new InvalidArgumentException(
                "Trying to create a response with an invalid status code."
            );
        }

        $response = clone $this;
        $response->statusCode = $code;
        if (is_null($reasonPhrase)) {
            $reasonPhrase = self::$recommendedReasonPhrases[$code];
        }
        $response->reasonPhrase = $reasonPhrase;
        return $response;
    }

    /**
     * Gets the response Reason-Phrase, a short textual description of
     * the Status-Code.
     *
     * Because a Reason-Phrase is not a required element in a response
     * Status-Line, the Reason-Phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or
     * those listed in the IANA HTTP Status Code Registry) for the response's
     * Status-Code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @return string|null Reason phrase, or null if unknown.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Returns the list of recommended Reason-Phrases
     *
     * @return array An associative array where keys are the status codes and
     *  values are the correspondent reason phrases.
     */
    public static function getRecommendedReasonPhrases()
    {
        return self::$recommendedReasonPhrases;
    }
}