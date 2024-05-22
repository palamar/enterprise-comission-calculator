<?php

declare(strict_types=1);

namespace CommissionCalculator\Api;

interface OptionProviderInterface
{
    public function getInputFileLocation(): array;
}