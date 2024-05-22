<?php

declare(strict_types=1);

namespace CommissionCalculator\Api;

use CommissionCalculator\Dto\CountryDto;

interface CommissionRateProviderInterface
{
    public function getByCountry(CountryDto $country): float;
}