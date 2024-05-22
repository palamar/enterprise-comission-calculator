<?php

declare(strict_types=1);

namespace CommissionCalculator\Dto;

final class CountryDto
{
    public function __construct(
        public readonly string $alpha2,
    ) {
    }
}
