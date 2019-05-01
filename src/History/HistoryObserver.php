<?php


namespace Slepic\Http\Transfer\History;

use Psr\Http\Message\RequestInterface;
use Slepic\Http\Transfer\Log\StorageInterface;
use Slepic\Http\Transfer\Observer\ObserverDelegateInterface;
use Slepic\Http\Transfer\Observer\ObserverInterface;

/**
 * Class HistoryObserver
 * @package Slepic\Http\Transfer\History
 *
 * This observer creates a LogInterface instance and pushes it to a StorageInterface when the transfer is finished.
 */
final class HistoryObserver implements ObserverInterface
{
	/**
	 * @var StorageInterface
	 */
	private $storage;

	/**
	 * HistoryObserver constructor.
	 * @param StorageInterface $storage
	 */
	public function __construct(StorageInterface $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * @param RequestInterface $request
	 * @param array $context
	 * @return ObserverDelegateInterface
	 */
	public function observe(RequestInterface $request, array $context = [])
	{
		return new HistoryObserverDelegate(
			$this->storage,
			$request,
			$context
		);
	}
}