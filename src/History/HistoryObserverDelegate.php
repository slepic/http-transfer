<?php

namespace Slepic\Http\Transfer\History;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slepic\Http\Transfer\Log\Log;
use Slepic\Http\Transfer\Log\StorageInterface;
use Slepic\Http\Transfer\Observer\AbstractObserverDelegate;

/**
 * This observer creates a LogInterface instance and pushes it to a StorageInterface when the transfer is finished.
 */
final class HistoryObserverDelegate extends AbstractObserverDelegate
{
    private float $startTime;

    public function __construct(private StorageInterface $storage, private RequestInterface $request, private array $context = [])
    {
        $this->startTime = \microtime(true);
    }

    protected function finish(?ResponseInterface $response = null, \Throwable $exception = null): void
    {
        $log = new Log(
            $this->request,
            $this->startTime,
            \microtime(true),
            $response,
            $exception,
            $this->context
        );
        $this->storage->store($log);
    }
}
