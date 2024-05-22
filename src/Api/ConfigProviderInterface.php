<?php

declare(strict_types=1);

namespace CommissionCalculator\Api;

interface ConfigProviderInterface
{
    public function getCountryResolverUrl(): string;

    public function getRateResolverUrl(): string;

    public function getRateResolverApiKey(): string;
}