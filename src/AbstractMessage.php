<?php

namespace Opportus\Http\Message;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

use \InvalidArgumentException;

/**
 * The abstract message...
 *
 * @version 0.0.1
 * @package Opportus\Http\Message
 * @author  Clément Cazaud <opportus@gmail.com>
 */
abstract class AbstractMessage implements MessageInterface
{
	/**
	 * Retrieves the HTTP protocol version as a string.
	 *
	 * @return string
	 */
	public function getProtocolVersion()
	{
		return $this->protocolVersion;
	}

	/**
	 * Returns an instance with the specified HTTP protocol version.
	 *
	 * @param  string $version HTTP protocol version
	 * @return static $clone
	 */
	public function withProtocolVersion($version)
	{
		$clone = clone $this;
		$clone->protocolVersion = $version;

		return $clone;
	}

	/**
	 * Gets all message header values.
	 *
	 * @return array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Checks if a header exists by the given case-insensitive name.
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function hasHeader($name)
	{
		$name    = strtolower($name);
		$headers = array_change_key_case($this->headers);

		return isset($headers[$name]);
	}

	/**
	 * Gets a message header value by the given case-insensitive name.
	 *
	 * @param  string $name
	 * @return array
	 */
	public function getHeader($name)
	{
		$name    = strtolower($name);
		$headers = array_change_key_case($this->headers);

		return isset($headers[$name]) ? $headers[$name] : array();
	}

	/**
	 * Gets a comma-separated string of the values for a single header.
	 *
	 * @param  string $name
	 * @return string
	 */
	public function getHeaderLine($name)
	{
		$name    = strtolower($name);
		$headers = array_change_key_case($this->headers);

		return isset($headers[$name]) ? implode(',', $headers[$name]) : '';
	}

	/**
	 * Returns an instance with the provided value replacing the specified header.
	 *
	 * @param  string       $name
	 * @param  string|array $value
	 * @return static       $clone
	 * @throws \InvalidArgumentException for invalid header names or values
	 */
	public function withHeader($name, $value)
	{
		if (! is_string($name)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument.');	
		}

		if (! is_string($value) && ! is_array($value)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string or array as second argument.');
		}

		$clone = clone $this;

		if ($clone->headers) {
			foreach ($clone->headers as $headerName => $values) {
				if (strtolower($name) === strtolower($headerName)) {
					unset($clone->headers[$headerName]);
					break;
				}
			}
		}

		$clone->headers[$name] = is_string($value) ? explode(',', $value) : $value;

		return $clone;
	}

	/**
	 * Returns an instance with the specified header appended with the given value.
	 *
	 * @param  string       $name
	 * @param  string|array $value
	 * @return static       $clone
	 * @throws \InvalidArgumentException for invalid header names or values
	 */
	public function withAddedHeader($name, $value)
	{
		if (! is_string($name)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument.');	
		}

		if (! is_string($value) && ! is_array($value)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string or array as second argument.');
		}

		$clone = clone $this;
		$value = is_string($value) ? explode(',', $value) : $value;

		if ($clone->headers) {
			foreach ($clone->headers as $headerName => $values) {
				if (strtolower($name) === strtolower($headerName)) {
					$clone->headers[$headerName] = array_merge($clone->headers[$headerName], $value);

					return $clone;
				}
			}
		}

		$clone->headers[$name] = $value;

		return $clone;
	}

	/**
	 * Returns an instance without the specified header.
	 *
	 * @param  string $name
	 * @return static $clone
	 */
	public function withoutHeader($name)
	{
		$clone = clone $this;

		foreach ($clone->headers as $headerName => $values) {
			if (strtolower($name) === strtolower($headerName)) {
				unset($clone->headers[$headerName]);
				break;
			}
		}

		return $clone;
	}

	/**
	 * Gets the body of the message.
	 *
	 * @return StreamInterface
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * Returns an instance with the specified message body.
	 *
	 * @param  StreamInterface $body
	 * @return static          $clone
	 * @throws \InvalidArgumentException When the body is not valid.
	 */
	public function withBody(StreamInterface $body)
	{
		$clone = clone $this;
		$clone->body = $body;

		return $clone;
	}
}

