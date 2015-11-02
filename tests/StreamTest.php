<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests;

use Fsilva\HttpMessage\Stream;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * StreamTest test case
 *
 * @package Fsilva\HttpMessage\Tests
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class StreamTest extends TestCase 
{

    public function validStreams()
    {
        return [
            'stdin' => ['php://stdin', 'r'],
            'tmp' => ['php://temp', 'r+'],
            'input' => ['php://input']
        ];
    }

    public function invalidStreams()
    {
        return [
            'object' => [new \stdClass()],
            'number' => [123],
            'boolean' => [false],
            'array' => [[]]
        ];
    }

    /**
     * @dataProvider validStreams
     * @param        $stream
     * @param string $mode
     */
    public function testCreateStream($stream, $mode = 'r')
    {
        $streamable = new Stream($stream, $mode);
        $this->assertInstanceOf("Psr\\Http\\Message\\StreamInterface", $streamable);
    }

    /**
     * @dataProvider invalidStreams
     * @param $stream
     */
    public function testInvalidStreamCreation($stream)
    {
        $this->setExpectedException(
            'Fsilva\\HttpMessage\\Exception\\InvalidArgumentException'
        );
        new Stream($stream);
    }

    public function testToString()
    {

        $fp = fopen('php://temp', 'x+');
        fputs($fp, 'This is a test');
        $stream = new Stream($fp);
        $this->assertEquals('This is a test', (string) $stream);
    }

    public function testWriteTiStream()
    {
        $stream = new Stream('php://temp', 'w+');
        $this->assertFalse($stream->eof());
        $this->assertTrue($stream->isSeekable());
        $stream->write('Another test');
        $this->assertEquals('Another test', $stream->getContents());
        $this->assertEquals(12, $stream->tell());
        $this->assertTrue($stream->eof());
        $this->assertTrue($stream->rewind());
        $this->assertFalse($stream->eof());
        $this->assertEquals(0, $stream->tell());
        $this->assertEquals('Another', $stream->read(7));
        $this->assertNull($stream->getSize());
        $this->assertTrue(is_array($stream->getMetadata()));
        $this->assertEquals('PHP', $stream->getMetadata('wrapper_type'));
        $this->assertNull($stream->getMetadata('__some__key'));
        $stream->close();
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->read(10));
    }
}
