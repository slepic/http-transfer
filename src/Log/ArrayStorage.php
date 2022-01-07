<?php

namespace Slepic\Http\Transfer\Log;

/**
 * Simple implementation of StorageInterface that allows to read the logs through an IteratorAggregate.
 */
class ArrayStorage implements StorageInterface, \IteratorAggregate, \Countable, \ArrayAccess
{
    private \ArrayObject $logs;

    public function __construct(array|\ArrayAccess $storage = [])
    {
        $this->logs = new \ArrayObject($storage);
    }

    public function store(LogInterface $log): void
    {
        $this->logs[] = $log;
    }

    /**
     * @return \Iterator<LogInterface>
     */
    public function getIterator(): \Iterator
    {
        return $this->logs->getIterator();
    }

    public function count(): int
    {
        return \count($this->logs);
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->logs[$offset]);
    }

    /**
     * @param int $offset
     * @return LogInterface|null
     */
    public function offsetGet($offset): ?LogInterface
    {
        return $this->logs[$offset];
    }

    /**
     * @param mixed $offset
     * @param LogInterface $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            throw new \Exception('Logs can only be appended.');
        }
        $this->store($value);
    }

    public function offsetUnset($offset): void
    {
        throw new \BadMethodCallException('Cannot unset logs.');
    }
}
