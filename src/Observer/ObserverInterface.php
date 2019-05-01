<?php

namespace Slepic\Http\Transfer\Observer;

use Psr\Http\Message\RequestInterface;

/**
 * Interface ObserverInterface
 * @package Slepic\Http\Transfer
 *
 * This interface provides mean to watch over HTTP transfers.
 *
 * Implementors will probably need to implement both ObserverInterface and ObserverDelegateInterface, since they work closely together.
 * The ObserverDelegateInterface exists to provide single transfer observer and is destroyed when the transfer, it is attached to, is completed.
 */
interface ObserverInterface
{
	/**
	 * @param RequestInterface $request
	 * @param array $context
	 * @return ObserverDelegateInterface
	 */
	public function observe(RequestInterface $request, array $context = []);
}