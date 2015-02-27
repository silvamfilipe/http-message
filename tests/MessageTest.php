<?php

/**
 * This file is part of fsilva/http-message package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Test;

use Fsilva\HttpMessage\Message;

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
        'Pragma' => ['Pragma: no-cache'],
        'Accept-Encoding' => ['gzip', 'deflate'],
        'Cookie' => ['$Version=1', '$Skin=new'],
    ];

    /** @var  Message */
    private $message;

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
            'Pragma1' => ['Pragma', 'PragmaVlue'],
            'Pragma2' => ['Pragma', $this->testHeaders['Pragma'][0]],
            'Accept-Encoding' => ['Accept-Encoding', $this->testHeaders['Accept-Encoding']],
            'Cookie' => ['Cookie', $this->testHeaders['Cookie']],
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

}
