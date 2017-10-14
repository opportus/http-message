<?php

namespace Opportus\Http\Message;

use Psr\Http\Message\StreamInterface;

use \RuntimeException;

/**
 * The stream...
 *
 * @version 0.0.1
 * @package Opportus\Psr7
 * @author  Clément Cazaud <opportus@gmail.com>
 */
class Stream implements StreamInterface
{
	/**
	 * @var ressource $data
	 */
	protected $data;

	/**
	 * @var array $meta
	 */
	protected $meta;

	/**
	 * Constructor.
	 *
	 * @param resource $resource
	 */
	public function __construct($resource)
	{
		$this->init($resource);
	}

	/**
	 * Initializes the stream.
	 *
	 * @param resource $resource
	 */
	protected function init($resource)
	{
		$this->data = $resource;
		$this->meta = stream_get_meta_data($this->data);
	}

	/**
	 * Reads all data from the stream into a string, from the beginning to end.
	 *
	 * @return string
	 */
	public function __toString()
	{
		try {
			if ($this->isSeekable()) {
				$this->rewind();
			}

			return $this->getContents();

		} catch (RuntimeException $e) {
			return '';
		}	
	}

	/**
	 * Closes the stream and any underlying resources.
	 */
	public function close()
	{
		$resource = $this->detach();

		fclose($resource);
	}

	/**
	 * Separates any underlying resources from the stream.
	 *
	 * @return ressource|null
	 */
	public function detach()
	{
		$resource = $this->data;

		unset($this->data);

		return $resource;
	}

	/**
	 * Gets the size of the stream if known.
	 *
	 * @return int|null
	 */
	public function getSize()
	{
		$stats = fstat($this->data);

		return $stats['size'];
	}

	/**
	 * Returns the current position of the file read/write pointer.
	 *
	 * @return int
	 * @throws \RuntimeException on error
	 */
	public function tell()
	{
		if ($position = ftell($this->data)) {
			return $position;
		}

		throw new RuntimeException('Unable to tell the pointer\'s current position of the stream.');
	}

	/**
	 * Returns true if the stream is at the end of the stream.
	 *
	 * @return bool
	 */
	public function eof()
	{
		return feof($this->data);
	}

	/**
	 * Returns whether or not the stream is seekable.
	 *
	 * @return bool
	 */
	public function isSeekable()
	{
		return $this->meta['seekable'];
	}

	/**
	 * Seeks to a position in the stream.
	 *
	 * @param int $offset
	 * @param int $whence Default:SEEK_SET
	 * @throws \RuntimeException on failure
	 */
	public function seek($offset, $whence = SEEK_SET)
	{
		if (! $this->isSeekable()) {
			throw new RuntimeException('The stream is not seekable.');

		} elseif (fseek($this->data, $offset, $whence) === -1) {
			throw new RuntimeException('Unable to seek to a position in the stream.');
		}
	}

	/**
	 * Seeks to the beginning of the stream.
	 *
	 * @throws \RuntimeException on failure
	 */
	public function rewind()
	{
		$this->seek(0);
	}

	/**
	 * Returns whether or not the stream is writable.
	 *
	 * @return bool
	 */
	public function isWritable()
	{
		$hash = array_flip(array(
			'r',
			'w+',
			'r+',
			'x+',
			'c+',
			'rb',
			'w+b',
			'r+b',
			'x+b',
			'c+b',
			'rt',
			'w+t',
			'r+t',
			'x+t',
			'c+t',
			'a+',
		));

		return isset($hash[$this->meta['mode']]);
	}

	/**
	 * Writes data to the stream.
	 *
	 * @param  string $string
	 * @return int
	 * @throws \RuntimeException on failure
	 */
	public function write($string)
	{
		if (! $this->isWritable()) {
			throw new RuntimeException('The stream is not writable');

		} elseif ($size = fwrite($this->data, $string)) {
			return $size;
		}

		throw new RuntimeException('Unable to write to the stream.');
	}

	/**
	 * Returns whether or not the stream is readable.
	 *
	 * @return bool
	 */
	public function isReadable()
	{
		$hash = array_flip(array(
			'w',
			'w+',
			'rw',
			'r+',
			'x+',
			'c+',
			'wb',
			'w+b',
			'r+b',
			'x+b',
			'c+b',
			'w+t',
			'r+t',
			'x+t',
			'c+t',
			'a',
			'a+',
		));

		return isset($hash[$this->meta['mode']]);
	}

	/**
	 * Reads data from the stream.
	 *
	 * @param  int $length
	 * @return string
	 * @throws \RuntimeException on failure
	 */
	public function read($length)
	{
		if (! $this->isReadable()) {		
			throw new RuntimeException('The stream is not readable.');

		} elseif ($size = fread($this->data, $length)) {
			return $size;
		}

		throw new RuntimeException('Unable to write to the stream.');
	}

	/**
	 * Gets the remaining contents in a string.
	 *
	 * @return string
	 * @throws \RuntimeException if unable to read or an error occurs while reading
	 */
	public function getContents()
	{
		if ($content = stream_get_contents($this->data)) {
			return $content;
		}

		throw new RuntimeException('Unable to get stream\'s content.');
	}

	/**
	 * Gets stream metadata as an associative array or retrieve a specific key.
	 *
	 * @param  string $key Default:null
	 * @return mixed
	 */
	public function getMetadata($key = null)
	{
		if (isset($this->meta[$key])) {
			return $this->meta[$key];

		} elseif (null === $key) {
			return $this->meta;

		} else {
			return null;
		}
	}
}

