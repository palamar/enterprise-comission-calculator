<?php

declare(strict_types=1);

namespace CommissionCalculator\Api;
use RuntimeException;
use LogicException;

interface TransactionsFileReaderInterface
{
    /**
     * @throws RuntimeException
     * @throws LogicException
     */
    public function iterate(string $filePath): \Iterator;
}
