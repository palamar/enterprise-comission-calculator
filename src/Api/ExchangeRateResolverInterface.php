<?php

declare (strict_types = 1);

namespace CommissionCalculator\Api;

use CommissionCalculator\Dto\CurrencyRateDto;
use CommissionCalculator\Exception\RateServerUnreachableException;
use CommissionCalculator\Exception\CantGetRateException;

interface ExchangeRateResolverInterface
{
    /**
     * @throws RateServerUnreachableException
     * @throws CantGetRateException
     */
    public function getRateToEUR(string $currency): CurrencyRateDto;
}
