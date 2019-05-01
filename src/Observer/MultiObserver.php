<?php


namespace Slepic\Http\Transfer\Observer;


use Psr\Http\Message\RequestInterface;

/**
 * Class MultiObserver
 * @package Slepic\Http\Transfer\Observer
 *
 * Allows multiple observers to be processed at the same time.
 */
class MultiObserver implements ObserverInterface
{
	/**
	 * @var ObserverInterface[]|\Traversable
	 */
	private $observers;

	/**
	 * MultiObserver constructor.
	 * @param ObserverInterface[]|\Traversable $observers
	 * @throws \Exception
	 */
	public function __construct($observers)
	{
		if (!\is_array($observers) || !$observers instanceof \Traversable) {
			throw new \Exception('Expected array or iterator.');
		}
		$this->observers = $observers;
	}

	/**
	 * @param RequestInterface $request
	 * @param array $context
	 * @return ObserverDelegateInterface
	 */
	public function observe(RequestInterface $request, array $context = [])
	{
		$delegates = [];
		foreach( $this->observers as $observer) {
			$delegates[] = $observer->observe($request, $context);
		}
		return new MultiObserverDelegate($delegates);
	}
}