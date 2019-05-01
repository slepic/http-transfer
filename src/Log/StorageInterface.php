<?php


namespace Slepic\Http\Transfer\Log;

/**
 * Interface StorageInterface
 * @package Slepic\Http\Transfer\Log
 *
 * Storage for LogInterface instances.
 */
interface StorageInterface
{
	/**
	 * @param LogInterface $log
	 * @return void
	 */
	public function store(LogInterface $log);
}