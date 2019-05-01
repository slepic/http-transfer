<?php


namespace Slepic\Http\Transfer\Observer;

use Psr\Http\Message\ResponseInterface;

/**
 * Class MultiObserverDelegate
 * @package Slepic\Http\Transfer\Observer
 *
 * Allows multiple observers to be processed at the same time.
 */
class MultiObserverDelegate implements ObserverDelegateInterface
{
    /**
     * @var ObserverDelegateInterface[]|\Traversable
     */
    private $delegates;

    /**
     * MultiObserverDelegate constructor.
     * @param ObserverDelegateInterface[]|\Traversable $delegates
     */
    public function __construct($delegates)
    {
        if (!\is_array($delegates) || !$delegates instanceof \Traversable) {
            throw new \Exception('Expected array or iterator.');
        }
        $this->delegates = $delegates;
    }

    /**
     * @param ResponseInterface $response
     */
    public function success(ResponseInterface $response)
    {
        foreach ($this->delegates as $delegate) {
            $delegate->success($response);
        }
    }

    /**
     * @param \Exception $exception
     * @param ResponseInterface|null $response
     */
    public function error(\Exception $exception, ResponseInterface $response = null)
    {
        foreach ($this->delegates as $delegate) {
            $delegate->error($exception, $response);
        }
    }
}
