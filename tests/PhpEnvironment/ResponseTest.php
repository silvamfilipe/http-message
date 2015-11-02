<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests\PhpEnvironment;

use Fsilva\HttpMessage\PhpEnvironment\Response;
use Fsilva\HttpMessage\Stream;

/**
 * ResponseTest test case
 *
 * @package Fsilva\HttpMessage\Tests\PhpEnvironment
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @coverage Response
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @coverage Response::headersSent
     * @runInSeparateProcess
     *
     * @return Response
     */
    public function testHeadersWereSent()
    {
        $response = new Response();
        $response = $response->withHeader('content-type', 'application/json');
        $response->sendHeaders();

        $this->assertTrue($response->headersSent());
        return $response;
    }

    /**
     * @param Response $response
     * @depends testHeadersWereSent
     *
     * @return Response
     */
    public function testSendingHeadersAlwaysReturnsResponse($response)
    {
        $this->assertInstanceOf(
            "Fsilva\\HttpMessage\\PhpEnvironment\\Response",
            $response->sendHeaders()
        );
        return $response;
    }

    /**
     * @depends testSendingHeadersAlwaysReturnsResponse
     * @param Response $response
     */
    public function testSendOutputsContent($response)
    {
        $body = new Stream\Buffer();
        $body->write("Hello world!");
        $response = $response->withBody($body);
        $this->expectOutputString("Hello world!");
        $response->send();
        $this->assertAttributeEquals(true,'contentSent', $response);
    }
}