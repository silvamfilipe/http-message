<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage\Tests\Stream;

use Fsilva\HttpMessage\Stream\Buffer;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * BufferTest test case
 *
 * @package Fsilva\HttpMessage\Tests\Stream
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BufferTest extends TestCase 
{

    /**
     * Check Buffer stream and its defaults
     * @test
     */
    public function checkBufferStreamAndItsDefaults()
    {
        $buffer = new Buffer(25);
        $this->assertEquals(25, $buffer->getMetadata('hwm'));
        $this->assertNull($buffer->getMetadata('foo'));
        $this->assertEquals([], $buffer->getMetadata());
        $this->assertEquals(0, $buffer->getSize());
        $this->assertFalse($buffer->tell());
        $this->assertTrue($buffer->eof());
        $this->assertFalse($buffer->isSeekable());
        $this->assertTrue($buffer->isReadable());
        $this->assertTrue($buffer->isWritable());
        $this->assertFalse($buffer->seek(10));
        $this->assertFalse($buffer->rewind());
    }

    /**
     * Reads and write test
     * @test
     */
    public function readAndWriteToBuffer()
    {
        $buffer = new Buffer(12);

        $this->assertEquals(10, $buffer->write('Valid test'));
        $this->assertEquals('Valid test', (string) $buffer);
        $this->assertFalse($buffer->write('Other'));
        $this->assertEquals('Valid', $buffer->read(5));
        $this->assertEquals(10, $buffer->getSize());
        $this->assertFalse($buffer->eof());
        $this->assertEquals(' testOther', $buffer->read(30));
        $this->assertEquals(0, $buffer->getSize());
        $this->assertTrue($buffer->eof());
        $buffer->write('Some val');
        $this->assertEquals('Some val', $buffer->getContents());

        $buffer->detach();
        $this->assertEmpty((string) $buffer);
    }
}
