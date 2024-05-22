<?php

declare(strict_types=1);

namespace CommissionCalculator\Dto;

final class CurrencyRateDto
{
    public function __construct(
        public readonly string $currency,
        public readonly float $rate,
    ) {
    }
}
