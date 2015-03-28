<?php

/**
 * This file is part of HttpMessage package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fsilva\HttpMessage;

use Psr\Http\Message\StreamableInterface;
use Fsilva\HttpMessage\Exception\InvalidArgumentException;

/**
 * Implementation of PSR HTTP Streams
 *
 * @package Fsilva\HttpMessage
 */
class Stream implements StreamableInterface
{

    /**
     * @var resource Stream resource
     */
    protected $resource;

    /**
     * @var array Stream metadata
     */
    private $metadata;

    /**
     * Creates a streamable instance for given stream
     *
     * @param string|resource $stream
     * @param string          $mode   Mode with which to open stream
     *
     * @throws InvalidArgumentException If the stream parameter given is not
     *                                  a string or a resource.
     */
    public function __construct($stream, $mode = 'r')
    {
        $notValid = !is_string($stream) && !is_resource($stream);
        if ($notValid) {
            throw new InvalidArgumentException(
                'Invalid stream provided; must be a string stream ' .
                'identifier or resource'
            );
        }

        $this->resource = $stream;

        if (is_string($stream)) {
            $this->resource = fopen($stream, $mode);
        }
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method attempts to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if ($this->resource) {
            $resource = $this->detach();
            fclose($resource);
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * Get the size of the stream if known
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        return null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int|bool Position of the file pointer or false on error.
     */
    public function tell()
    {
        $position = false;
        if ($this->resource) {
            $position = ftell($this->resource);
        }
        return $position;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        $eof = true;
        if ($this->resource) {
            $eof = feof($this->resource);
        }
        return $eof;
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        $seekable = false;
        if ($this->resource) {
            $seekable = $this->getMetadata('seekable');
        }
        return $seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     *
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *                    based on the seek offset. Valid values are identical to the built-in
     *                    PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *                    offset bytes SEEK_CUR: Set position to current location plus offset
     *                    SEEK_END: Set position to end-of-stream plus offset.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $result = false;
        if ($this->resource && $this->isSeekable()) {
            $result = fseek($this->resource, $offset, $whence);
            $result = (0 === $result);
        }
        return $result;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will return FALSE, indicating
     * failure; otherwise, it will perform a seek(0), and return the status of
     * that operation.
     *
     * @see  seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function rewind()
    {
        return $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        $readable = false;
        if ($this->resource) {
            $mode = $this->getMetadata('mode');
            $regExp = '/(r\+|a|w|x|c)/i';
            $readable = (bool) preg_match($regExp, $mode);
        }
        return $readable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     *
     * @return int|bool Returns the number of bytes written to the stream on
     *     success or FALSE on failure.
     */
    public function write($string)
    {
        $bitesWritten = false;
        if ($this->resource && $this->isWritable()) {
            $bitesWritten = fwrite($this->resource, $string);
        }
        return $bitesWritten;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        $readable = false;
        if ($this->resource) {
            $mode = $this->getMetadata('mode');
            $regExp = '/(r\+|r|w\+|a\+|x\+|c\+)/i';
            $readable = (bool) preg_match($regExp, $mode);
        }
        return $readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *                    them. Fewer than $length bytes may be returned if underlying stream
     *                    call returns fewer bytes.
     *
     * @return string|false Returns the data read from the stream, false if
     *     unable to read or if an error occurs.
     */
    public function read($length)
    {
        if (!$this->resource || !$this->isReadable()) {
            return false;
        }

        $contents = '';
        if (!$this->eof()) {
            $contents = fread($this->resource, $length);
        }
        return $contents;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     */
    public function getContents()
    {
        $string = '';
        if ($this->isReadable()) {
            $string = stream_get_contents($this->resource, -1, 0);
        }
        return (string) $string;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     *
     * @param string $key Specific metadata to retrieve.
     *
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (is_null($this->metadata)) {
            $this->metadata = stream_get_meta_data($this->resource);
        }
        $return = null;
        if (is_null($key)) {
            $return = $this->metadata;
        }
        if (array_key_exists($key, $this->metadata)) {
            $return = $this->metadata[$key];
        }
        return $return;
    }
}
