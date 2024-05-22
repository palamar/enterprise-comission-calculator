<?php

namespace CommissionCalculator\Domain;

use CommissionCalculator\Api\CommissionRateProviderInterface;

use CommissionCalculator\Dto\CountryDto;

class CommissionRateProvider implements CommissionRateProviderInterface
{
    private const float DEFAULT_RATE = 0.02;
    private const float DEFAULT_EU_RATE = 0.01;

    public function getByCountry(CountryDto $country): float
    {
        // TODO: it's actually not all countries that are part of the EU for now, but let it be.
        return match ($country->alpha2) {
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE',
            'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT',
            'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO',
            'SE', 'SI', 'SK' => self::DEFAULT_EU_RATE,
            default => self::DEFAULT_RATE,
        };
    }
}
