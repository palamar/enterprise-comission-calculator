<?php

declare(strict_types=1);

namespace CommissionCalculator\Exception;

class NegativeRateException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Currency Rate cannot be negative.');
    }
}
