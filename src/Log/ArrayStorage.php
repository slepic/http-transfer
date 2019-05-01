<?php

namespace Slepic\Http\Transfer\Log;

/**
 * Class ArrayStorage
 * @package Slepic\Http\Transfer\Log
 *
 * Simple implementation of StorageInterface that allows to read the logs through an IteratorAggregate.
 */
class ArrayStorage implements StorageInterface, \IteratorAggregate, \Countable, \ArrayAccess
{
	/**
	 * @var \ArrayObject
	 */
	private $logs;

	/**
	 * ArrayStorage constructor.
	 * @param array|\ArrayAccess|null $storage
	 */
	public function __construct($storage = [])
	{
		$this->logs = new \ArrayObject($storage);
	}

	/**
	 * @param LogInterface $log
	 */
	public function store(LogInterface $log)
	{
		$this->logs[] = $log;
	}

	/**
	 * @return \Iterator
	 */
	public function getIterator()
	{
		return $this->logs->getIterator();
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return \count($this->logs);
	}

	/**
	 * @param int $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->logs[$offset]);
	}

	/**
	 * @param int $offset
	 * @return LogInterface|null
	 */
	public function offsetGet($offset)
	{
		return $this->logs[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param LogInterface $value
	 * @throws \Exception
	 */
	public function offsetSet($offset, $value)
	{
		if ($offset !== null) {
			throw new \Exception('Logs can only be appended.');
		}
		$this->store($value);
	}

	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException('Cannot unset logs.');
	}
}