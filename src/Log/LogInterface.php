<?php


namespace Slepic\Http\Transfer\Log;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface LogInterface
 * @package Slepic\Http\Transfer\Log
 *
 * Describes a single transfer log.
 */
interface LogInterface
{
	/**
	 * Get the initiator request.
	 *
	 * @return RequestInterface
	 */
	public function getRequest();

	/**
	 * Get the response.
	 *
	 * If there is not an exception, there must be a response.
	 * If there is an exception, the response is optional.
	 *
	 * @return ResponseInterface|null
	 */
	public function getResponse();

	/**
	 * Get exception that occurred while trying to process the request.
	 *
	 * If there is not a response, there must be an exception.
	 * If there is a response, the exception is optional.
	 *
	 * @return \Exception|null
	 */
	public function getException();

	/**
	 * Get start time of the transfer process.
	 *
	 * @return float Unix timestamp with microseconds
	 */
	public function getStartTime();

	/**
	 * Get end time of the transfer process
	 *
	 * @return float Unix timestamp with microseconds
	 */
	public function getEndTime();

	/**
	 * Get the transfer context
	 *
	 * @return array Arbitrary data describing the context in which the HTTP transfer occurred.
	 */
	public function getContext();
}