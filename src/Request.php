<?php

namespace Opportus\Http\Message;

use Opportus\Http\Message\AbstractMessage;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

use \InvalidArgumentException;

/**
 * The request...
 *
 * @version 0.0.1
 * @package Opportus\Http\Message
 * @author  Clément Cazaud <opportus@gmail.com>
 */
class Request extends AbstractMessage implements RequestInterface
{
	/**
	 * @var string $protocolVersion
	 */
	protected $protocolVersion;

	/**
	 * @var string $method
	 */
	protected $method;

	/**
	 * @var array $headers
	 */
	protected $headers;

	/**
	 * @var StreamInterface $body
	 */
	protected $body;

	/**
	 * @var UriInterface $uri
	 */
	protected $uri;

	/**
	 * @var string $requestTarget
	 */
	protected $requestTarget;

	/**
	 * Constructor.
	 *
	 * @param string          $protocolVersion
	 * @param string          $method
	 * @param array           $headers
	 * @param StreamInterface $body
	 * @param UriInterface    $uri
	 * @param string          $requestTarget   Default:null
	 */
	public function __construct(string $protocolVersion, string $method, array $headers, StreamInterface $body, UriInterface $uri, string $requestTarget = null)
	{
		$this->init($protocolVersion, $method, $headers, $body, $uri, $requestTarget);
	}

	/**
	 * Initializes the request.
	 *
	 * @param string          $protocolVersion
	 * @param string          $method
	 * @param array           $headers
	 * @param StreamInterface $body
	 * @param UriInterface    $uri
	 * @param string          $requestTarget   Default:null
	 */
	protected function init(string $protocolVersion, string $method, array $headers, StreamInterface $body, UriInterface $uri, string $requestTarget = null)
	{
		$this->protocolVersion = $protocolVersion;
		$this->method          = $method;

		foreach ($headers as $header => $value) {
			$this->headers[$header] = (array) $value;
		}

		$this->body          = $body;
		$this->uri           = $uri;
		$this->requestTarget = is_null($requestTarget) ? $this->getRequestTarget() : $requestTarget;

		if (! $this->getHeader('Host') && $host = $this->uri->getHost()) {
			$this->headers['Host'] = (array) $host;
		}
	}

	/**
	 * Gets the message's request target.
	 *
	 * @return string
	 */
	public function getRequestTarget()
	{
		if (isset($this->requestTarget)) {
			return $this->requestTarget;

		} elseif (is_null($this->uri)) {
			return '/';
		}

		$path  = $this->uri->getPath();
		$query = $this->uri->getQuery();

		$target  = $path ? $path : '/';
		$target .= $query ? '?' . $query : $target;

		return $target;
	}

	/**
	 * Returns an instance with the specific request-target.
	 *
	 * @param  mixed            $requestTarget
	 * @return RequestInterface
	 */
	public function withRequestTarget($requestTarget)
	{
		$clone = clone $this;
		$clone->requestTarget = $requestTarget;

		return $clone;
	}

	/**
	 * Gets the HTTP method of the request.
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Returns an instance with the provided HTTP method.
	 *
	 * @param  string           $method
	 * @return RequestInterface
	 * @throws \InvalidArgumentException for invalid HTTP methods
	 */
	public function withMethod($method)
	{
		switch ($method) {
			case 'GET':
			case 'HEAD':
			case 'POST':
			case 'PUT':
			case 'DELETE':
			case 'CONNECT':
			case 'OPTIONS':
			case 'TRACE':
			case 'PATCH':
				break;
			default:
				throw new InvalidArgumentException('Invalid HTTP method.');

		}

		$clone = clone $this;
		$clone->method = $method;

		return $clone;
	}

	/**
	 * Gets the URI instance.
	 *
	 * @return UriInterface
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * Returns an instance with the provided URI.
	 *
	 * @param  UriInterface     $uri
	 * @param  bool             $preserveHost Default:false
	 * @return RequestInterface
	 */
	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		$hostUri = $uri->getHost();

		$clone = clone $this;
		$clone->uri = $uri;

		if (false === $preserveHost && $hostUri) {
			$clone->headers['Host'] = (array) $hostUri;
		}

		if (true === $preserveHost) {
			if (! $clone->getHeader('Host') && $hostUri) {
				$clone->headers['Host'] = (array) $hostUri;
			}
		}

		return $clone;
	}
}

