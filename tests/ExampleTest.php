<?php


namespace Slepic\Tests\Http\Transfer;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slepic\Http\Transfer\Log\ArrayStorage;
use Slepic\Http\Transfer\History\HistoryObserver;
use Slepic\Http\Transfer\Log\LogInterface;
use Slepic\Http\Transfer\Observer\ObserverInterface;

class ExampleTest extends TestCase
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface|null $response
     * @param \Exception|null $exception
     * @param int|null $time
     * @param array $context
     *
     * @dataProvider provideTransfers
     */
    public function testTransfer(
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $exception = null,
        $time = null,
        array $context = []
    ) {
        //setup services
        $storage = new ArrayStorage();
        $observer = new HistoryObserver($storage);

        //observe request
        $this->transfer($observer, $request, $response, $exception, $time, $context);

        //make assertions
        $this->assertCount(1, $storage);
        $this->assertArrayHasKey(0, $storage);
        /** @var LogInterface $log */
        $log = $storage[0];
        $this->assertInstanceOf(LogInterface::class, $log);
        $this->assertSame($request, $log->getRequest());
        $this->assertSame($response, $log->getResponse());
        $this->assertSame($exception, $log->getException());
        $this->assertSame($context, $log->getContext());
        $this->assertInternalType('float', $log->getStartTime());
        $this->assertInternalType('float', $log->getEndTime());
        $this->assertTrue($log->getStartTime() < $log->getEndTime());
        $this->assertEquals($time / 1000000.0, $log->getEndTime() - $log->getStartTime(), '', 0.001);
    }

    private function transfer(
        ObserverInterface $observer,
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $exception = null,
        $time = null,
        array $context = []
    ) {
        //observe request
        $delegate = $observer->observe($request, $context);

        //emulate transfer time
        if ($time !== null) {
            \usleep($time);
        }

        //finish transfer
        if ($exception !== null) {
            $delegate->error($exception, $response);
        } else {
            $delegate->success($response);
        }
    }

    public function provideTransfers()
    {
        $defaultTime = 100;
        $defaultContext = [\md5(\time()) => \md5(\time())];
        return [
            [
                $this->createMock(RequestInterface::class),
                $this->createMock(ResponseInterface::class),
                null,
                $defaultTime,
                $defaultContext,
            ],
            [
                $this->createMock(RequestInterface::class),
                $this->createMock(ResponseInterface::class),
                new \Exception(),
                $defaultTime,
                $defaultContext,
            ],
            [
                $this->createMock(RequestInterface::class),
                null,
                new \Exception(),
                $defaultTime,
                $defaultContext,
            ],
        ];
    }
}
