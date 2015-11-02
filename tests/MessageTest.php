<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Test;

use Fsilva\HttpMessage\Message;
use Fsilva\HttpMessage\Stream\Buffer;

/**
 * Message test case
 *
 * @package Fsilva\HttpMessage\Test
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{

    /** @var array Sample test headers */
    private $testHeaders = [
        'User-Agent' => ['Mozilla/5.0 (X11; Linux x86_64; rv:12.0) Gecko/20100101 Firefox/21.0'],
        'Referer' => ['http://en.wikipedia.org/wiki/Main_Page'],
        'Pragma' => ['no-cache'],
        'Accept-Encoding' => ['gzip', 'deflate'],
        'Cookie' => ['$Version=1', '$Skin=new'],
    ];

    /** @var  Message */
    private $message;

    /**
     * Invalid message headers
     * @return array
     */
    public function invalidHeaders()
    {
        return [
            'bad-name' => [null, 'test'],
            'bool-name' => [true, 'test'],
            'integer-name' => [2, 'test'],
            'object-name' => [new \stdClass(), 'test'],
            'array-name' => [['test'], 'test'],
            'bad-value' => ['content-type', null],
            'bool-value' => ['content-type', false],
            'integer-value' => ['content-type', 2],
            'object-value' => ['content-type', new \stdClass()],
            'badArray-value' => ['content-type', [new \stdClass()]],
        ];
    }

    /**
     * Valid header arguments
     *
     * @return array
     */
    public function validHeaders()
    {
        return [
            'User-Agent' => ['User-Agent', $this->testHeaders['User-Agent'][0]],
            'Referer' => ['Referer', $this->testHeaders['Referer'][0]],
            'Pragma1' => ['Pragma', 'PragmaValue'],
            'Pragma2' => ['Pragma', $this->testHeaders['Pragma'][0]],
            'Accept-Encoding' => ['Accept-Encoding', $this->testHeaders['Accept-Encoding']],
            'Cookie' => ['Cookie', $this->testHeaders['Cookie']],
        ];
    }

    /**
     * Bad header names
     * @return array
     */
    public function invalidHeaderNames()
    {
        return [
            'bad-name' => [null],
            'bool-name' => [true],
            'integer-name' => [2],
            'object-name' => [new \stdClass()],
            'array-name' => [['test']],
        ];
    }

    /**
     * Invalid values for HTTP protocol version
     *
     * @return array
     */
    public function invalidVersions()
    {
        return [
            'empty' => [''],
            'single-digit' => ['1'],
            'to-heigh' => ['3.4'],
            'bool' => [true],
            'array' => [['array']]
        ];
    }

    /**
     * Valid HTTP protocols versions
     *
     * @return array
     */
    public function validVersions()
    {
        return [
            '1.0' => ['1.0'],
            '1.1' => ['1.1'],
            '2.0' => ['2.0'],
        ];
    }

    /**
     * Check that message implements PSR HTTP-Message interface
     */
    public function testCheckPsrHttpImplementation()
    {
        $message = new Message();
        $this->assertInstanceOf('Psr\\Http\\Message\\MessageInterface',  $message);
    }

    /**
     * @dataProvider invalidVersions
     * @param mixed $version
     */
    public function testCreateWithProtocolVersionFails($version)
    {
        $message = new Message();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidVersionException'
        );
        $message->withProtocolVersion($version);
    }

    /**
     * @dataProvider validVersions
     * @param $version
     */
    public function testCreateWithProtocolVersion($version)
    {
        $message = new Message();
        $newMessage = $message->withProtocolVersion($version);
        $this->assertNotSame($message, $newMessage);
        $this->assertInstanceOf('Fsilva\\HttpMessage\\Message', $newMessage);
        $this->assertEquals($version, $newMessage->getProtocolVersion());
    }

    /**
     * @dataProvider validHeaders
     *
     * @param string $name
     * @param string|array $value
     */
    public function testCreateWithHeader($name, $value)
    {
        if (is_null($this->message)) {
            $this->message = new Message();
        }
        $newMessage = $this->message->withHeader($name, $value);
        $this->assertInstanceOf('Fsilva\\HttpMessage\\Message', $newMessage);
        $this->assertNotSame($this->message, $newMessage);
        $this->assertTrue($newMessage->hasHeader(strtolower($name)));
        $value = is_string($value) ? [$value] : $value;
        $expected = implode(', ', $value);
        $this->assertEquals($expected, $newMessage->getHeader($name));
        $this->message = $newMessage;
    }

    /**
     * Test cumulative header assignment
     */
    public function testCumulativeHeaderCreation()
    {
        $message = new Message();

        foreach ($this->validHeaders() as $arguments) {
            $newMessage = $message->withHeader(reset($arguments), end($arguments));
            $this->assertNotSame($message, $newMessage);
            $this->assertTrue($newMessage->hasHeader(strtolower(reset($arguments))));
            $this->assertInstanceOf('Fsilva\\HttpMessage\\Message', $newMessage);
            $message = $newMessage;
        }

        $this->assertEquals($this->testHeaders, $message->getHeaders());
    }

    /**
     * @dataProvider invalidHeaders
     * @param mixed $name
     * @param mixed $value
     */
    public function testFailWithHeaderCreation($name, $value)
    {
        $message = new Message();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        $message->withHeader($name, $value);
    }

    /**
     * @dataProvider invalidHeaderNames
     * @param $name
     */
    public function testGetBadNameHeader($name)
    {
        $message = new Message();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        $message->getHeader($name);
    }

    public function testGetMissingHeader()
    {
        $message = new Message();
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\MissingHeaderException'
        );
        $message->getHeader('foo');
    }

    public function testWithAddedHeaderCreation()
    {
        $message = new Message();

        foreach ($this->validHeaders() as $arguments) {
            $newMessage = $message->withAddedHeader(reset($arguments), end($arguments));
            $this->assertNotSame($message, $newMessage);
            $this->assertTrue($newMessage->hasHeader(strtolower(reset($arguments))));
            $this->assertInstanceOf('Fsilva\\HttpMessage\\Message', $newMessage);
            $message = $newMessage;
        }
        $expected = $this->testHeaders;
        $expected['Pragma'] = ['PragmaValue', 'no-cache'];
        $this->assertEquals($expected, $message->getHeaders());
    }

    public function testCreateWithoutHeader()
    {
        $message = new Message();

        foreach ($this->validHeaders() as $arguments) {
            $newMessage = $message->withAddedHeader(reset($arguments), end($arguments));
            $this->assertNotSame($message, $newMessage);
            $this->assertTrue($newMessage->hasHeader(strtolower(reset($arguments))));
            $this->assertInstanceOf('Fsilva\\HttpMessage\\Message', $newMessage);
            $message = $newMessage;
        }
        $message = $message->withoutHeader('pragma');
        $expected = $this->testHeaders;
        unset($expected['Pragma']);
        $this->assertEquals($expected, $message->getHeaders());
    }

    public function testHasHeaderInvalidName()
    {
        $message = new Message();
        $message = $message->withHeader('Content-Type', 'text/html');
        $this->assertTrue($message->hasHeader('content-type'));
        $this->assertFalse($message->hasHeader(true));
    }

    public function testCreateWithBody()
    {
        $message = new Message();
        $newMessage = $message->withBody(new Buffer());
        $this->assertNotSame($message, $newMessage);
        $this->assertInstanceOf('Fsilva\\HttpMessage\\Message', $newMessage);
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $newMessage->getBody());
        $this->assertNull($message->getBody());
    }

}
