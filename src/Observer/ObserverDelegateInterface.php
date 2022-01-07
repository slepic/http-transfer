<?php

namespace Slepic\Http\Transfer\Observer;

use Psr\Http\Message\ResponseInterface;

/**
 * This interface is responsible for observing a single request-response transfer.
 * Implementations can expect a single call to start()
 * followed by a single call to either success() or error()
 * and after that these objects should not be referenced from anywhere
 * and just get destroyed.
 */
interface ObserverDelegateInterface
{
    /**
     * Observe successful completion of the transfer process.
     *
     * @param ResponseInterface $response
     * @return void
     */
    public function success(ResponseInterface $response): void;

    /**
     * Observe unsuccessful completion of the transfer process.
     *
     * @param \Exception $exception
     * @param ResponseInterface|null $response
     * @return void
     */
    public function error(\Throwable $exception, ?ResponseInterface $response = null): void;
}
