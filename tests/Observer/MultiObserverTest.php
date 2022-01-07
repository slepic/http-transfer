<?php

namespace Slepic\Tests\Http\Transfer\Observer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slepic\Http\Transfer\Observer\MultiObserver;
use Slepic\Http\Transfer\Observer\MultiObserverDelegate;
use Slepic\Http\Transfer\Observer\ObserverDelegateInterface;
use Slepic\Http\Transfer\Observer\ObserverInterface;

/**
 * This is actually test case for both MultiObserver and MultiObserverDelegate.
 */
class MultiObserverTest extends TestCase
{
    /**
     * Tests that MultiObserver::observe calls all contained observers and returns MultiObserverDelegate instance.
     *
     * @param iterable $delegates
     * @dataProvider provideData
     */
    public function testObserve(iterable $delegates): void
    {
        $request = $this->createMock(RequestInterface::class);
        $options = [\md5(\time()) => \md5(\time())];
        $observers = [];
        foreach ($delegates as $key => $delegate) {
            $observer = $this->createMock(ObserverInterface::class);
            $observer->expects($this->once())
                ->method('observe')
                ->with($request, $options)
                ->willReturn($this->delegate($delegate));
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
     * @param iterable $delegates
     * @dataProvider provideData
     */
    public function testObserveIterator(iterable $delegates): void
    {
        $request = $this->createMock(RequestInterface::class);
        $options = [\md5(\time()) => \md5(\time())];
        $observers = new \ArrayObject();
        foreach ($delegates as $key => $delegate) {
            $observer = $this->createMock(ObserverInterface::class);
            $observer->expects($this->once())
                ->method('observe')
                ->with($request, $options)
                ->willReturn($this->delegate($delegate));
            $observers[$key] = $observer;
        }
        $observer = new MultiObserver($observers);

        $delegate = $observer->observe($request, $options);
        $this->assertInstanceOf(MultiObserverDelegate::class, $delegate);
    }

    /**
     * Tests that MultiObserverDelegate::success calls all contained delegates.
     */
    public function testSuccess(): void
    {
        $delegates = [
            $this->createMock(ObserverDelegateInterface::class),
            $this->createMock(ObserverDelegateInterface::class),
        ];
        $response = $this->createMock(ResponseInterface::class);
        foreach ($delegates as $delegate) {
            $delegate->expects($this->exactly(1))
                ->method('success')
                ->with($response);
        }
        $delegate = new MultiObserverDelegate($delegates);

        $delegate->success($response);
    }

    /**
     * Tests that MultiObserverDelegate::error calls all contained delegates.
     */
    public function testError(): void
    {
        $delegates = [
            $this->createMock(ObserverDelegateInterface::class),
            $this->createMock(ObserverDelegateInterface::class),
        ];
        $exception = new \Exception();
        $response = $this->createMock(ResponseInterface::class);
        foreach ($delegates as $delegate) {
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
     * @param iterable $delegates
     * @dataProvider provideData
     */
    public function testAllDelegatesPassed(iterable $delegates): void
    {
        $exception = new \Exception();
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $options = [\md5(\time()) => \md5(\time())];
        $observers = [];
        foreach ($delegates as $key => $delegate) {
            $delegate = $this->delegate($delegate);
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

    public function provideData(): array
    {
        return [
            [[]],
            [
                [
                    $this->mockDelegate(),
                ],
            ],
            [
                [
                    $this->mockDelegate(),
                    $this->mockDelegate(),
                ],
            ],

            //test that MultiObserverDelegate can work with iterators
            [
                new \ArrayIterator([
                    $this->mockDelegate(),
                ]),
            ],
            [
                new \ArrayObject([
                    $this->mockDelegate(),
                ]),
            ],
        ];
    }

    private function mockDelegate(): \Closure
    {
        return fn () => $this->createMock(ObserverDelegateInterface::class);
    }

    private function delegate(MockObject|\Closure $delegate): MockObject
    {
        return $delegate instanceof \Closure ? $delegate() : $delegate;
    }
}
