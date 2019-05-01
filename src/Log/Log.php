<?php

namespace Slepic\Http\Transfer\Log;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Log
 * @package Slepic\Http\Transfer\Log
 */
class Log implements LogInterface
{
	/**
	 * @var RequestInterface
	 */
	private $request;

	/**
	 * @var ResponseInterface|null
	 */
	private $response;

	/**
	 * @var \Exception|null
	 */
	private $exception;

	/**
	 * @var float
	 */
	private $startTime;

	/**
	 * @var float
	 */
	private $endTime;

	/**
	 * @var array
	 */
	private $context;

	/**
	 * Log constructor.
	 * @param RequestInterface $request
	 * @param float $startTime
	 * @param float $endTime
	 * @param ResponseInterface|null $response
	 * @param \Exception|null $exception
	 * @param array $context
	 * @throws \Exception
	 */
	public function __construct(
		RequestInterface $request,
		$startTime,
		$endTime,
		ResponseInterface $response = null,
		\Exception $exception = null,
		array $context = []
	) {
		if ($response === null && $exception === null) {
			throw new \Exception('At least one of response/exception cannot be null.');
		}
		$this->request = $request;
		$this->response = $response;
		$this->exception = $exception;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->context = $context;
	}

	/**
	 * @return RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @return ResponseInterface|null
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * @return \Exception|null
	 */
	public function getException()
	{
		return $this->exception;
	}

	/**
	 * @return float
	 */
	public function getStartTime()
	{
		return $this->startTime;
	}

	/**
	 * @return float
	 */
	public function getEndTime()
	{
		return $this->endTime;
	}

	/**
	 * @return array
	 */
	public function getContext()
	{
		return $this->context;
	}
}