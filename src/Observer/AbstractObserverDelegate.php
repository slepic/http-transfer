<?php


namespace Slepic\Http\Transfer\Observer;

use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractObserverDelegate
 * @package Slepic\Http\Transfer\Observer
 *
 * This abstract class expects implementors to implement the protected finish() method
 * to complete the observation the same way for both success and error states.
 */
abstract class AbstractObserverDelegate implements ObserverDelegateInterface
{
	/**
	 * Final implementation that delegates to protected shared finish() method.
	 *
	 * @param ResponseInterface $response
	 */
	final public function success(ResponseInterface $response)
	{
		$this->finish($response);
	}

	/**
	 * Final implementation that delegates to protected shared finish() method.
	 *
	 * @param \Exception $exception
	 * @param ResponseInterface|null $response
	 */
	final public function error(\Exception $exception, ResponseInterface $response = null)
	{
		$this->finish($response, $exception);
	}

	/**
	 * Observe end of the transfer process.
	 *
	 * Implementations should define behaviour of this method, but should not call it themselves.
	 * If they do, they should check that either response or exception is provided before they call this method.
	 * On the other hand, implementations of this method may rely on the fact that at least one of them will be provided and not check it again.
	 *
	 * @param ResponseInterface|null $response
	 * @param \Exception|null $exception
	 * @return void
	 */
	abstract protected function finish(ResponseInterface $response = null, \Exception $exception = null);
}