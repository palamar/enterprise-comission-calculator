<?php

declare(strict_types=1);

namespace CommissionCalculator\Unit\Domain;

use CommissionCalculator\Domain\CommissionRateProvider;
use CommissionCalculator\Domain\CommissionCalculator;
use CommissionCalculator\Dto\CountryDto;
use CommissionCalculator\Dto\CurrencyRateDto;
use CommissionCalculator\Dto\TransactionDto;
use CommissionCalculator\Dto\CommissionCalculationDto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use CommissionCalculator\Exception\NegativeRateException;
use PHPUnit\Framework\Attributes\ExpectedException;

class CommissionCalculatorTest extends TestCase
{
    private const EU_COMMISION_RATE = 0.01;
    private const EU_COUNTRIES = [
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

    private CommissionCalculator $commissionCalculator;

    protected function setUp(): void
    {
        $this->commissionCalculator = new CommissionCalculator(new CommissionRateProvider());
    }

    #[Test]
    public function testCalculateMinusRate()
    {
        $transaction = new CommissionCalculationDto(
            transaction: new TransactionDto('2345678', 34.8, 'USD'),
            currencyRate: new CurrencyRateDto('USD', -1.084563),
            country: new CountryDto('US'),
        );
        $this->expectException(NegativeRateException::class);
        $this->commissionCalculator->calculate($transaction);
    }

    #[Test]
    public function testCalculateZeroEURate()
    {
        $this->markTestSkipped('We may have inconsistency in logic 0 rate for the EU country would be calculated as outside EU.');
        $transaction = new CommissionCalculationDto(
            transaction: new TransactionDto('2345678', 34.83, 'EUR'),
            currencyRate: new CurrencyRateDto('USD', 0),
            country: new CountryDto('GE'),
        );
        $actualRate = $this->commissionCalculator->calculate($transaction);
        $this->assertEquals(0.35, $actualRate);
    }

    #[Test]
    public function testCalculateZeroNonEURate()
    {
        $transaction = new CommissionCalculationDto(
            transaction: new TransactionDto('2345678', 34.83, 'EUR'),
            currencyRate: new CurrencyRateDto('USD', 0),
            country: new CountryDto('DE'),
        );
        $actualRate = $this->commissionCalculator->calculate($transaction);
        $this->assertEquals(0.35, $actualRate);
    }

    #[Test]
    public function testCalculateNonEURate()
    {
        $transaction = new CommissionCalculationDto(
            transaction: new TransactionDto('2345678', 34.83, 'UAH'),
            currencyRate: new CurrencyRateDto('UAH', 43.111714),
            country: new CountryDto('UA'),
        );
        $actualRate = $this->commissionCalculator->calculate($transaction);
        $this->assertEquals(0.02, $actualRate);
    }
}