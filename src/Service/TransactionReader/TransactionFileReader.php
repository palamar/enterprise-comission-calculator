<?php

declare(strict_types=1);

namespace CommissionCalculator\Service\TransactionReader;

use CommissionCalculator\Api\TransactionsFileReaderInterface;

class TransactionFileReader implements TransactionsFileReaderInterface
{
    private \SplFileObject $fileHandler;

    public function iterate(string $filePath): \Iterator
    {
        $this->fileHandler = new \SplFileObject($filePath);

        while (!$this->fileHandler->eof()) {
                $line = $this->fileHandler->fgets();
                if (!json_validate($line)) {
                    // log error
                    continue;
                }
                yield json_decode($line, true);
        }

        unset($this->fileHandler);
    }

    public function __destruct()
    {
        unset($this->fileHandler);
    }
}
