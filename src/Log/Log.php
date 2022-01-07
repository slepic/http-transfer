<?php

namespace Slepic\Http\Transfer\Log;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Log implements LogInterface
{
    public function __construct(
        private RequestInterface $request,
        private float $startTime,
        private float $endTime,
        private ?ResponseInterface $response = null,
        private ?\Throwable $exception = null,
        private array $context = []
    ) {
        if ($response === null && $exception === null) {
            throw new \Exception('At least one of response/exception cannot be null.');
        }
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getException(): ?\Throwable
    {
        return $this->exception;
    }

    public function getStartTime(): float
    {
        return $this->startTime;
    }

    public function getEndTime(): float
    {
        return $this->endTime;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
