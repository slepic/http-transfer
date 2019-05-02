<?php


namespace Slepic\Tests\Http\Transfer\Observer;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slepic\Http\Transfer\Observer\MultiObserver;
use Slepic\Http\Transfer\Observer\MultiObserverDelegate;
use Slepic\Http\Transfer\Observer\ObserverDelegateInterface;
use Slepic\Http\Transfer\Observer\ObserverInterface;

/**
 * Class MultiObserverTest
 * @package Slepic\Tests\Http\Transfer\Observer
 *
 * This is actually test case for both MultiObserver and MultiObserverDelegate.
 */
class MultiObserverTest extends TestCase
{
    /**
     * Tests that MultiObserver::observe calls all contained observers and returns MultiObserverDelegate instance.
     *
     * @param $delegates
     * @dataProvider provideData
     */
    public function testObserve($delegates)
    {
        $request = $this->createMock(RequestInterface::class);
        $options = [\md5(\time()) => \md5(\time())];
        $observers = [];
        foreach ($delegates as $key => $delegate) {
            $observer = $this->createMock(ObserverInterface::class);
            $observer->expects($this->once())
                ->method('observe')
                ->with($request, $options)
                ->willReturn($delegate);
            $observers[$key] = $observer;
        }
        $observer = new MultiObserver($observers);

        $delegate = $observer->observe($request, $options);
        $this->assertInstanceOf(MultiObserverDelegate::class, $delegate);
    }

    /**
     * Tests that MultiObserver::observe calls all contained observers and returns MultiObserverDelegate instance
     * while working with Iterator of observers rather then an array.
     *
     * @param $delegates
     * @dataProvider provideData
     */
    public function testObserveIterator($delegates)
    {
        $request = $this->createMock(RequestInterface::class);
        $options = [\md5(\time()) => \md5(\time())];
        $observers = new \ArrayObject();
        foreach ($delegates as $key => $delegate) {
            $observer = $this->createMock(ObserverInterface::class);
            $observer->expects($this->once())
                ->method('observe')
                ->with($request, $options)
                ->willReturn($delegate);
            $observers[$key] = $observer;
        }
        $observer = new MultiObserver($observers);

        $delegate = $observer->observe($request, $options);
        $this->assertInstanceOf(MultiObserverDelegate::class, $delegate);
    }

    /**
     * Tests that MultiObserverDelegate::success calls all contained delegates.
     *
     * @param $delegates
     * @dataProvider provideData
     */
    public function testSuccess($delegates)
    {
        $response = $this->createMock(ResponseInterface::class);
        foreach ($delegates as $key => $delegate) {
            $delegate->expects($this->once())
                ->method('success')
                ->with($response);
        }
        $delegate = new MultiObserverDelegate($delegates);

        $delegate->success($response);
    }

    /**
     * Tests that MultiObserverDelegate::error calls all contained delegates.
     *
     * @param $delegates
     * @throws \Exception
     * @dataProvider provideData
     */
    public function testError($delegates)
    {
        $exception = new \Exception();
        $response = $this->createMock(ResponseInterface::class);
        foreach ($delegates as $key => $delegate) {
            $delegate->expects($this->once())
                ->method('error')
                ->with($exception, $response);
        }
        $delegate = new MultiObserverDelegate($delegates);

        $delegate->error($exception, $response);
    }

    /**
     * Tests that all delegates are passed to the MultiObserverDelegate when created.
     *
     * Kinda repeats previous tests, but it is only way to test that all delegates are passed to MultiObserverDelegate
     * without having it expose that list, or accessing it through reflection.
     *
     * @param $delegates
     * @dataProvider provideData
     */
    public function testAllDelegatesPassed($delegates)
    {
        $exception = new \Exception();
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $options = [\md5(\time()) => \md5(\time())];
        $observers = [];
        foreach ($delegates as $key => $delegate) {
            $observer = $this->createMock(ObserverInterface::class);
            $observer->expects($this->once())
                ->method('observe')
                ->with($request, $options)
                ->willReturn($delegate);
            $observers[$key] = $observer;

            $delegate->expects($this->once())
                ->method('success')
                ->with($response);
            $delegate->expects($this->once())
                ->method('error')
                ->with($exception, $response);
        }
        $observer = new MultiObserver($observers);

        $delegate = $observer->observe($request, $options);
        $this->assertInstanceOf(MultiObserverDelegate::class, $delegate);

        //since we work with mocks we can break the api here and call both on same object
        $delegate->success($response);
        $delegate->error($exception, $response);
    }

    /**
     * @return array
     */
    public function provideData()
    {
        return [
            [[]],
            [
                [
                    $this->createMock(ObserverDelegateInterface::class),
                ],
            ],
            [
                [
                    $this->createMock(ObserverDelegateInterface::class),
                    $this->createMock(ObserverDelegateInterface::class),
                ],
            ],

            //test that MultiObserverDelegate can work with iterators
            [
                new \ArrayIterator([
                    $this->createMock(ObserverDelegateInterface::class),
                ]),
            ],
            [
                new \ArrayObject([
                    $this->createMock(ObserverDelegateInterface::class),
                ]),
            ],
        ];
    }
}
