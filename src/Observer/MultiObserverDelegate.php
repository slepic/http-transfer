<?php

namespace Slepic\Http\Transfer\Observer;

use Psr\Http\Message\ResponseInterface;

/**
 * Allows multiple observers to be processed at the same time.
 */
class MultiObserverDelegate implements ObserverDelegateInterface
{
    /**
     * @param iterable<ObserverDelegateInterface> $delegates
     */
    public function __construct(private iterable $delegates)
    {
    }

    public function success(ResponseInterface $response): void
    {
        foreach ($this->delegates as $delegate) {
            $delegate->success($response);
        }
    }

    public function error(\Throwable $exception, ?ResponseInterface $response = null): void
    {
        foreach ($this->delegates as $delegate) {
            $delegate->error($exception, $response);
        }
    }
}
