<?php

declare(strict_types=1);

namespace Service\TransactionReader;

use CommissionCalculator\Service\TransactionReader\TransactionFileReader;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TransactionFileReaderTest extends TestCase
{
    #[Test]
    public function testItearte()
    {
        $filePath = dirname(__FILE__) . '/input.txt';
        foreach ((new TransactionFileReader())->iterate($filePath) as $transaction) {
            $this->assertIsArray($transaction);
        }
    }
}