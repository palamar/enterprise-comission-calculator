<?php

declare(strict_types=1);

namespace CommissionCalculator\Dto;

final class CommissionCalculationDto
{
    public function __construct(
        public readonly TransactionDto  $transaction,
        public readonly CurrencyRateDto $currencyRate,
        public readonly CountryDto      $country,
    ) {
    }
}
