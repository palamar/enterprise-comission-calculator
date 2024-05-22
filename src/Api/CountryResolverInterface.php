<?php

declare(strict_types=1);

namespace CommissionCalculator\Api;

use CommissionCalculator\Dto\CountryDto;

interface CountryResolverInterface
{
    public function resolve(string $bin): CountryDto;
}
