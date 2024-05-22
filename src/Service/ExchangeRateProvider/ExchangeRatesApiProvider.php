<?php

declare(strict_types=1);

namespace CommissionCalculator\Service\ExchangeRateProvider;

use CommissionCalculator\Api\ExchangeRateResolverInterface;
use CommissionCalculator\Exception\CantGetRateException;
use CommissionCalculator\Exception\RateServerUnreachableException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use CommissionCalculator\Api\ConfigProviderInterface;
use CommissionCalculator\Dto\CurrencyRateDto;

class ExchangeRatesApiProvider implements ExchangeRateResolverInterface
{
    private ?array $rates;

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ConfigProviderInterface $config,
    ) {
    }

    public function getRateToEUR(string $currency): CurrencyRateDto
    {
        $currency = strtoupper($currency);
        if (isset($this->rates[$currency])) {
            if ($this->rates[$currency] === false) {
                throw new CantGetRateException('Can\'t get rate for currency ' . $currency);
            }
            return $this->rates[$currency];
        }

        try {
            $response = $this->client->request(
                'GET',
                sprintf(
                    '%s?access_key=%s',
                    $this->config->getRateResolverUrl(),
                    urlencode($this->config->getRateResolverApiKey()),
                ),
            );
        } catch (\Throwable) {
            throw new RateServerUnreachableException('Rate server is unreachable.');
        }

        if ($response->getStatusCode() !== 200) {
            throw new RateServerUnreachableException('Can\'t get data from the server.');
        }

        $options = $response->toArray();

        if (!isset($options['rates'])) {
            throw new CantGetRateException('Can\'t get data from the server.');
        }

        foreach ($options['rates'] as $rateCurrency => $rate) {
            $rateCurrency = strtoupper($rateCurrency);
            $this->rates[$rateCurrency] = new CurrencyRateDto($rateCurrency, (float) trim((string) $rate));
        }

        if (!isset($this->rates[$currency])) {
            $this->rates[$currency] = false;
            throw new CantGetRateException('Can\'t get rate for currency ' . $currency);
        }

        return $this->rates[$currency];
    }
}
