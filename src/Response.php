<?php

namespace Opportus\Http\Message;

use Opportus\Http\Message\AbstractMessage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use \InvalidArgumentException;

/**
 * The response...
 *
 * @version 0.0.1
 * @package Opportus\Psr7
 * @author  Clément Cazaud <opportus@gmail.com>
 */
class Response extends AbstractMessage implements ResponseInterface
{
	/**
	 * @var string $protocolVersion
	 */
	protected $protocolVersion;

	/**
	 * @var int $statusCode
	 */
	protected $statusCode;

	/**
	 * @var string $reasonPhrase
	 */
	protected $reasonPhrase;

	/**
	 * @var array $headers
	 */
	protected $headers;

	/**
	 * @var StreamInterface $body
	 */
	protected $body;

	/**
	 * Constructor.
	 *
	 * @param string          $protocolVersion
	 * @param int             $statusCode
	 * @param array           $headers         Default:array()
	 * @param StreamInterface $body
	 * @param string          $reasonPhrase    Default:null
	 */
	public function __construct(string $protocolVersion, int $statusCode, array $headers = array(), StreamInterface $body, string $reasonPhrase = null)
	{
		$this->init($protocolVersion, $statusCode, $headers, $body, $reasonPhrase);
	}

	/**
	 * Initializes the response.
	 *
	 * @param string          $protocolVersion
	 * @param int             $statusCode
	 * @param array           $headers         Default:array()
	 * @param StreamInterface $body
	 * @param string          $reasonPhrase    Default:null
	 */
	protected function init(string $protocolVersion, int $statusCode, array $headers = array(), StreamInterface $body, string $reasonPhrase = null)
	{
		$this->protocolVersion = $protocolVersion;
		$this->statusCode      = $statusCode;

		foreach ($headers as $header => $value) {
			$this->headers[$header] = (array) $value;	
		}

		$this->body            = $body;
		$this->reasonPhrase    = is_null($reasonPhrase) ? $this->getReasonPhrase() : $reasonPhrase;
	}

	/**
	 * Gets the response status code.
	 *
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * Return an instance with the specified status code and, optionally, reason phrase.
	 *
	 * @param  int    $code
	 * @param  string $reasonPhrase Default:''
	 * @return static
	 * @throws \InvalidArgumentException For invalid status code arguments
	 */
	public function withStatus($code, $reasonPhrase = '')
	{
		if (! preg_match('/^[1-5][0-9]{2}$/', $code) && ! is_int($code)) {
			throw new InvalidArgumentException('Invalid status code.');
		}

		$clone = clone $this;
		$clone->statusCode = $code;
		$clone->reasonPhrase = $reasonPhrase ? (string) $reasonPhrase : $clone->getReasonPhrase();

		return $clone;
	}

	/**
	 * Gets the response reason phrase associated with the status code.
	 *
	 * @return string
	 */
	public function getReasonPhrase()
	{
		if (! is_null($this->reasonPhrase)) {
			return $this->reasonPhrase;
		}

		$statusReasonMap = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-status',
			208 => 'Already Reported',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Switch Proxy',
			307 => 'Temporary Redirect',
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
			414 => 'Request-URI Too Large',
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
			451 => 'Unavailable For Legal Reasons',
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
		);

		$status = $this->getStatusCode();

		return isset($statusReasonMap[$status]) ? $statusReasonMap[$status] : null;
	}

	/**
	 * Sends the HTTP response.
	 */
	public function send()
	{
		http_response_code($this->getStatusCode());

		if (! is_null($this->getHeaders())) {
			foreach ($this->getHeaders() as $header => $values) {
				foreach ($values as $value) {
					header(sprintf('%s: %s', $header, $value), false);
				}
			}
		}

		if ($this->getBody()->getSize()) {
			echo $this->getBody();
		}
	}
}

