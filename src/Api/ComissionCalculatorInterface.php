<?php

namespace CommissionCalculator\Api;

use CommissionCalculator\Dto\CommissionCalculationDto;

interface ComissionCalculatorInterface
{
    public function calculate(CommissionCalculationDto $trnCommissionData): float;
}