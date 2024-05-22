<?php

declare(strict_types=1);

namespace CommissionCalculator\Unit\Domain;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use CommissionCalculator\Dto\CountryDto;
use CommissionCalculator\Domain\CommissionRateProvider;
use PHPUnit\Framework\Attributes\Test;

class CommissionRateProviderTest extends TestCase
{
    private const float EU_COMMISION_RATE = 0.01;
    private const float NON_EU_COMMISION_RATE = 0.02;
    private const array EU_COUNTRIES = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
        ];

    private const array NON_EU_COUNTRIES = [
        'UA',
        'UK',
    ];
    private CommissionRateProvider $commissionRateProvider;

    protected function setUp(): void
    {
        $this->commissionRateProvider = new CommissionRateProvider();
    }

    #[Test]
    #[DataProvider('euCountriesProvider')]
    public function getCommissionRateForEUCountry($country)
    {
        $actualRate = $this->commissionRateProvider->getByCountry($country);
        $this->assertEquals(self::EU_COMMISION_RATE, $actualRate);
    }

    public static function euCountriesProvider()
    {
        foreach (self::EU_COUNTRIES as $country) {
            yield [new CountryDto(
                alpha2: $country
            )];
        }
    }

    #[Test]
    #[DataProvider('nonEUCountriesProvider')]
    public function getCommissionRateForNonEUCountry($country)
    {
        $actualRate = $this->commissionRateProvider->getByCountry($country);
        $this->assertEquals(self::NON_EU_COMMISION_RATE, $actualRate);
    }

    public static function nonEUCountriesProvider()
    {
        foreach (self::NON_EU_COUNTRIES as $country) {
            yield [new CountryDto(
                alpha2: $country
            )];
        }
    }
}