<?php

namespace Slepic\Http\Transfer\History;

use Psr\Http\Message\RequestInterface;
use Slepic\Http\Transfer\Log\StorageInterface;
use Slepic\Http\Transfer\Observer\ObserverDelegateInterface;
use Slepic\Http\Transfer\Observer\ObserverInterface;

/**
 * This observer creates a LogInterface instance and pushes it to a StorageInterface when the transfer is finished.
 */
final class HistoryObserver implements ObserverInterface
{
    public function __construct(private StorageInterface $storage)
    {
    }

    public function observe(RequestInterface $request, array $context = []): ObserverDelegateInterface
    {
        return new HistoryObserverDelegate(
            $this->storage,
            $request,
            $context
        );
    }
}
