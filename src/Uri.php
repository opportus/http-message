<?php

namespace Opportus\Http\Message;

use Psr\Http\Message\UriInterface;

use \InvalidArgumentException;

/**
 * The URI...
 *
 * @version 0.0.1
 * @package Opportus\Psr7
 * @author  Clément Cazaud <opportus@gmail.com>
 */
class Uri implements UriInterface
{
	/**
	 * @var string $scheme
	 */
	protected $scheme = '';

	/**
	 * @var string $user
	 */
	protected $user = '';

	/**
	 * @var string $pass
	 */
	protected $pass = '';

	/**
	 * @var string $host
	 */
	protected $host = '';

	/**
	 * @var null|int $port
	 */
	protected $port = null;

	/**
	 * @var string $path
	 */
	protected $path = '';

	/**
	 * @var string $query
	 */
	protected $query = '';

	/**
	 * @var string $fragment
	 */
	protected $fragment = '';

	/**
	 * @var string $userInfo
	 */
	protected $userInfo = '';

	/**
	 * @var string $authority
	 */
	protected $authority = '';

	/**
	 * Constructor.
	 *
	 * @param string $scheme
	 * @param string $user
	 * @param string $pass
	 * @param string $host
	 * @param int    $port
	 * @param string $path
	 * @param string $query
	 * @param string $fragment
	 */
	public function __construct(string $scheme, string $user, string $pass, string $host, int $port, string $path, string $query, string $fragment)
	{
		$this->init($scheme, $user, $pass, $host, $port, $path, $query, $fragment);
	}

	/**
	 * Initializes the URI.
	 **
	 * @param string $scheme
	 * @param string $user
	 * @param string $pass
	 * @param string $host
	 * @param int    $port
	 * @param string $path
	 * @param string $query
	 * @param string $fragment
	 */
	protected function init(string $scheme, string $user, string $pass, string $host, int $port, string $path, string $query, string $fragment)
	{
		$this->scheme    = $this->normalizeScheme($scheme);
		$this->user      = $this->normalizeUser($user);
		$this->pass      = $this->normalizePass($pass);
		$this->host      = $this->normalizeHost($host);
		$this->port      = $this->normalizePort($port);
		$this->path      = $this->normalizePath($path);
		$this->query     = $this->normalizeQuery($query);
		$this->fragment  = $this->normalizeFragment($fragment);
		$this->userInfo  = $this->normalizeUserInfo($this->user, $this->pass);
		$this->authority = $this->normalizeAuthority($this->host, $this->user, $this->pass, $this->port);
	}

	/**
	 * Gets the scheme component of the URI.
	 *
	 * @return string
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * Gets the user component of the URI.
	 *
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Gets the pass component of the URI.
	 *
	 * @return string
	 */
	public function getPass()
	{
		return $this->pass;
	}

	/**
	 * Gets the host component of the URI.
	 *
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Gets the port component of the URI.
	 *
	 * @return null|int
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * Gets the path component of the URI.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Gets the query component of the URI.
	 *
	 * @return string
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * Gets the fragment component of the URI.
	 *
	 * @return string
	 */
	public function getFragment()
	{
		return $this->fragment;
	}

	/**
	 * Gets the user information component of the URI.
	 *
	 * @return string
	 */
	public function getUserInfo()
	{
		return $this->userInfo;
	}

	/**
	 * Gets the authority component of the URI.
	 *
	 * @return string
	 */
	public function getAuthority()
	{
		return $this->authority;
	}

	/**
	 * Returns an instance with the specified scheme.
	 *
	 * @param  string $scheme
	 * @return static $clone
	 */
	public function withScheme($scheme)
	{
		$clone = clone $this;
		$clone->scheme = $clone->normalizeScheme($scheme);

		return $clone;
	}

	/**
	 * Returns an instance with the specified user.
	 *
	 * @param  string $user
	 * @return static $clone
	 */
	public function withUser($user)
	{
		$clone = clone $this;
		$clone->user = $clone->normalizeUser($user);

		return $clone;
	}

	/**
	 * Returns an instance with the specified password.
	 *
	 * @param  string $pass
	 * @return static $clone
	 */
	public function withPass($pass)
	{
		$clone = clone $this;
		$clone->pass = $clone->normalizePass($pass);

		return $clone;
	}

	/**
	 * Returns an instance with the specified host.
	 *
	 * @param  string $host
	 * @return static $clone
	 */
	public function withHost($host)
	{
		$clone = clone $this;
		$clone->host = $clone->normalizeHost($host);

		return $clone;
	}

	/**
	 * Returns an instance with the specified port.
	 *
	 * @param  int|null $port
	 * @return static   $clone
	 */
	public function withPort($port)
	{
		$clone = clone $this;
		$clone->port = $clone->normalizePort($port);

		return $clone;
	}

	/**
	 * Returns an instance with the specified path.
	 *
	 * @param  string $path
	 * @return static $clone
	 */
	public function withPath($path)
	{
		$clone = clone $this;
		$clone->path = $clone->normalizePath($path);

		return $clone;
	}

	/**
	 * Returns an instance with the specified query.
	 *
	 * @param  string $query
	 * @return static $clone
	 */
	public function withQuery($query)
	{
		$clone = clone $this;
		$clone->query = $clone->normalizeQuery($query);

		return $clone;
	}

	/**
	 * Returns an instance with the specified fragment.
	 *
	 * @param  string $fragment
	 * @return static $clone
	 */
	public function withFragment($fragment)
	{
		$clone = clone $this;
		$clone->fragment = $clone->normalizeFragment($fragment);

		return $clone;
	}

	/**
	 * Returns an instance with the specified user info.
	 *
	 * @param  string      $user
	 * @param  string|null $pass  Default:null
	 * @return static      $clone
	 */
	public function withUserInfo($user, $pass = null)
	{
		$clone = clone $this;
		$clone->userInfo = $clone->normalizeUserInfo($user, $pass);

		return $clone;
	}

	/**
	 * Returns an instance with the specified authority.
	 *
	 * @param  string      $authority
	 * @param  string|null $host      Default:null
	 * @param  string|null $pass      Default:null
	 * @param  int|null    $port      Default:null
	 * @return static      $clone
	 */
	public function withAuthority($host, $user = null, $pass = null, $port = null)
	{
		$clone = clone $this;
		$clone->authority = $clone->normalizeAuthority($host, $user, $pass, $port);

		return $clone;
	}

	/**
	 * Normalizes the scheme component of the URI.
	 *
	 * @param  string $scheme
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function normalizeScheme($scheme)
	{
		if (! is_string($scheme)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument');	
		}

		return strtolower($scheme);
	}

	/**
	 * Normalizes the user component of the URI.
	 *
	 * @param  string $user
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function normalizeUser($user)
	{
		if (! is_string($user)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument');	
		}

		return $user;
	}

	/**
	 * Normalizes the pass component of the URI.
	 *
	 * @param  string $pass
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function normalizePass($pass)
	{
		if (! is_string($pass)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument');	
		}

		return $pass;
	}

	/**
	 * Normalizes the host component of the URI.
	 *
	 * @param  string $host
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function normalizeHost($host)
	{
		if (! is_string($host)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument');
		}

		return strtolower($host);
	}

	/**
	 * Normalizes the port component of the URI.
	 *
	 * @param  int      $port
	 * @return int|null
	 * @throws InvalidArgumentException
	 */
	public function normalizePort($port)
	{
		if ((! is_int($port) && ! is_null($port)) || ($port < 1 && $port > 0xFFFF)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only integer in range from 1 to 0xFFFF or null as first argument');
		}

		$scheme = $this->getScheme();

		if (is_null($port)) {
			return $port;
		}

		$schemePortMap = array(
			'acap' => 674,
			'afp' => 548,
			'dict' => 2628,
			'dns' => 53,
			'file' => NULL,
			'ftp' => 21,
			'git' => 9418,
			'gopher' => 70,
			'http' => 80,
			'https' => 443,
			'imap' => 143,
			'ipp' => 631,
			'ipps' => 631,
			'irc' => 194,
			'ircs' => 6697,
			'ldap' => 389,
			'ldaps' => 636,
			'mms' => 1755,
			'msrp' => 2855,
			'msrps' => NULL,
			'mtqp' => 1038,
			'nfs' => 111,
			'nntp' => 119,
			'nntps' => 563,
			'pop' => 110,
			'prospero' => 1525,
			'redis' => 6379,
			'rsync' => 873,
			'rtsp' => 554,
			'rtsps' => 322,
			'rtspu' => 5005,
			'sftp' => 22,
			'smb' => 445,
			'snmp' => 161,
			'ssh' => 22,
			'steam' => NULL,
			'svn' => 3690,
			'telnet' => 23,
			'ventrilo' => 3784,
			'vnc' => 5900,
			'wais' => 210,
			'ws' => 80,
			'wss' => 443,
			'xmpp' => NULL,
		);

		if ($scheme && $port !== (int) $schemePortMap[$scheme]) {
			return $port;
		}

		return null;
	}

	/**
	 * Normalizes the path component of the URI.
	 *
	 * @param  string $path
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function normalizePath($path)
	{
		if (! is_string($path)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument');	
		}

		return preg_replace_callback(
			'/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
			function ($matches) {
				return rawurlencode($matches[0]);
			},
			$path
		);
	}

	/**
	 * Normalizes the query component of the URI.
	 *
	 * @param  string $query
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function normalizeQuery($query)
	{
		if (! is_string($query)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument');	
		}

		return preg_replace_callback(
			'/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
			function ($matches) {
				return rawurlencode($matches[0]);
			},
			$query
		);
	}

	/**
	 * Normalizes the fragment component of the URI.
	 *
	 * @param  string $fragment
	 * @return string
	 * @throws InvalidArgumentException
	 */
	public function normalizeFragment($fragment)
	{
		if (! is_string($fragment)) {
			throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . '() accepts only string as first argument');	
		}

		return preg_replace_callback(
			'/(?:[^a-zA-Z0-9_\-\.~!\$&\'\(\)\*\+,;=%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
			function ($matches) {
				return rawurlencode($matches[0]);
			},
			$fragment
		);
	}

	/**
	 * Normalizes the user information component of the URI.
	 *
	 * @param  string      $user
	 * @param  string|null $pass
	 * @return string
	 */
	public function normalizeUserInfo($user, $pass)
	{
		$pass = is_null($pass) ? $this->getPass() : $this->normalizePass($pass);

		if ($userInfo = $this->normalizeUser($user)) {
			if ($pass) {
				$userInfo .= ':' . $pass;
			}
		}

		return $userInfo;

	}

	/**
	 * Normalizes the authority component of the URI.
	 *
	 * @param  string      $host
	 * @param  string|null $user
	 * @param  string|null $pass
	 * @param  string|null $port
	 * @return string
	 */
	public function normalizeAuthority($host, $user, $pass, $port)
	{
		$host = $this->normalizeHost($host);
		$user = is_null($user) ? $this->getUser() : $this->normalizeUser($user);
		$pass = is_null($pass) ? $this->getPass() : $this->normalizePass($pass);
		$port = is_null($port) ? $this->getPort() : $this->normalizePort($port);

		$userInfo = $this->normalizeUserInfo($user, $pass);

		$authority  = $userInfo ? $userInfo . '@' : '';
		$authority .= $host;
		$authority .= $port ? ':' . $port : '';

		return $authority;
	}

	/**
	 * Returns the string representation as a URI reference.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$scheme    = $this->getScheme();
		$authority = $this->getAuthority();
		$path      = $this->getPath();
		$query     = $this->getQuery();
		$fragment  = $this->getFragment();

		$string  = $scheme ? $scheme . ':' : $scheme;
		$string .= $authority ? '//' . $authority : $authority;

		if ($path) {
			if ($authority) {
				$string .= (0 === strpos($path, '/')) ? $path : '/' . $path;

			} else {
				$string .= (0 === strpos($path, '//')) ? '/' . ltrim($path, '/') : $path;
			}
		}

		$string .= $query ? '?' . $query : $query;
		$string .= $fragment ? '#' . $fragment : $fragment;

		return $string;
	}
}

