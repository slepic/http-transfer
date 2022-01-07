<?php

namespace Slepic\Http\Transfer\Observer;

use Psr\Http\Message\RequestInterface;

/**
 * Allows multiple observers to be processed at the same time.
 */
class MultiObserver implements ObserverInterface
{
    /**
     * MultiObserver constructor.
     * @param iterable<ObserverInterface> $observers
     * @throws \Exception
     */
    public function __construct(private iterable $observers)
    {
    }

    public function observe(RequestInterface $request, array $context = []): ObserverDelegateInterface
    {
        $delegates = [];
        foreach ($this->observers as $observer) {
            $delegates[] = $observer->observe($request, $context);
        }
        return new MultiObserverDelegate($delegates);
    }
}
