<?php

declare(strict_types=1);

namespace CommissionCalculator\Service\Config;

use CommissionCalculator\Api\ConfigProviderInterface;
use Symfony\Component\Dotenv\Dotenv;
class ConfigProvider implements ConfigProviderInterface
{
    private readonly Dotenv $dotenv;

    public function __construct() {
        $this->dotenv = new Dotenv();
        $sampleFile = dirname(__FILE__) . '/../../../.env.sample';
        if (file_exists($sampleFile)) {
            $this->dotenv->load($sampleFile);
        }
        $envFile = dirname(__FILE__) . '/../../../.env';
        if (file_exists($envFile)) {
            $this->dotenv->load($envFile);
        }
    }

    public function getCountryResolverUrl(): string
    {
        $url = (string) $_ENV['BINLIST_URL'] ?? '';
        return $this->normalize($url);
    }

    public function getRateResolverUrl(): string
    {
        $url = (string) $_ENV['EXCHANGERATESAPI_URL'] ?? '';
        return $this->normalize($url);
    }

    public function getRateResolverApiKey(): string
    {
        return (string) $_ENV['EXCHANGERATESAPI_ACCESS_KEY'] ?? '';
    }

    private function normalize(string $url): string
    {
        return trim($url, '/');
    }
}