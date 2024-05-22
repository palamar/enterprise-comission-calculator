<?php

declare(strict_types=1);

namespace CommissionCalculator\Dto;

final class TransactionDto
{
    public function __construct(
        public readonly string $bin,
        public readonly float $amount,
        public readonly string $currency,
    ) {
    }
}