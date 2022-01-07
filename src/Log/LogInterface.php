<?php

namespace Slepic\Http\Transfer\Log;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Describes a single transfer log.
 */
interface LogInterface
{
    /**
     * Get the initiator request.
     *
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * Get the response.
     *
     * If there is not an exception, there must be a response.
     * If there is an exception, the response is optional.
     *
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface;

    /**
     * Get exception that occurred while trying to process the request.
     *
     * If there is not a response, there must be an exception.
     * If there is a response, the exception is optional.
     *
     * @return \Throwable|null
     */
    public function getException(): ?\Throwable;

    /**
     * Get start time of the transfer process.
     *
     * @return float Unix timestamp with microseconds
     */
    public function getStartTime(): float;

    /**
     * Get end time of the transfer process
     *
     * @return float Unix timestamp with microseconds
     */
    public function getEndTime(): float;

    /**
     * Get the transfer context
     *
     * @return array Arbitrary data describing the context in which the HTTP transfer occurred.
     */
    public function getContext(): array;
}
