<?php

namespace Slepic\Http\Transfer\Log;

/**
 * Storage for LogInterface instances.
 */
interface StorageInterface
{
    public function store(LogInterface $log): void;
}
