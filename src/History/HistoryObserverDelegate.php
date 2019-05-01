<?php

namespace Slepic\Http\Transfer\History;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slepic\Http\Transfer\Log\Log;
use Slepic\Http\Transfer\Log\StorageInterface;
use Slepic\Http\Transfer\Observer\AbstractObserverDelegate;

/**
 * Class HistoryObserver
 * @package Slepic\Http\Transfer\History
 *
 * This observer creates a LogInterface instance and pushes it to a StorageInterface when the transfer is finished.
 */
final class HistoryObserverDelegate extends AbstractObserverDelegate
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var float
     */
    private $startTime;

    /**
     * @var array
     */
    private $context;

    /**
     * HistoryObserverDelegate constructor.
     * @param StorageInterface $storage
     * @param RequestInterface $request
     * @param array $context
     */
    public function __construct(StorageInterface $storage, RequestInterface $request, array $context = [])
    {
        $this->startTime = \microtime(true);
        $this->storage = $storage;
        $this->request = $request;
        $this->context = $context;
    }

    /**
     * @param ResponseInterface|null $response
     * @param \Exception|null $exception
     * @throws \Exception
     */
    protected function finish(ResponseInterface $response = null, \Exception $exception = null)
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
