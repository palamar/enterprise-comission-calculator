<?php

namespace CommissionCalculator\Domain;

use CommissionCalculator\Api\ComissionCalculatorInterface;
use CommissionCalculator\Api\CommissionRateProviderInterface;
use CommissionCalculator\Dto\CommissionCalculationDto;
use CommissionCalculator\Exception\NegativeRateException;

class CommissionCalculator implements ComissionCalculatorInterface
{
    private const string EUR = 'EUR';

    public function __construct(
        private readonly CommissionRateProviderInterface $commissionRateProvider,
    ) {
    }

    /**
     * @throws NegativeRateException
     */
    public function calculate(CommissionCalculationDto $trnCommissionData): float
    {
        if ($trnCommissionData->currencyRate->rate < .0) {
            throw new NegativeRateException();
        }

        $commissionRate = $this->commissionRateProvider->getByCountry($trnCommissionData->country);

        if ($this->isZeroRate($trnCommissionData)) {
            // TODO: we may have failure here as it's possible to have 0 rate for the country inside EU
            //      in such case we would calculate commission as for country outside EU.
            return $this->formatCalculation($trnCommissionData->transaction->amount * $commissionRate);
        }

        // we already handled the case when rate is zero in the isZeroRate function, so we can't have division by zero here.
        $amountInCurrency = $trnCommissionData->transaction->amount / $trnCommissionData->currencyRate->rate;

        return $this->formatCalculation($amountInCurrency * $commissionRate);
    }

    private function isZeroRate(CommissionCalculationDto $trnCommissionData): bool
    {
        // TODO: I'm not sure that rate equals to zero means that we should use the amount as is
        return $trnCommissionData->transaction->currency === self::EUR
            || $trnCommissionData->currencyRate->rate == 0;
    }

    private function formatCalculation(float $result): float
    {
        return ceil($result * 100) / 100;
    }
}
