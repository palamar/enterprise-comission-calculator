<?php

declare(strict_types=1);

namespace CommissionCalculator\Service\CountryResolver;

use CommissionCalculator\Api\CountryResolverInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use CommissionCalculator\Api\ConfigProviderInterface;
use CommissionCalculator\Exception\CantGetCountryException;
use CommissionCalculator\Dto\CountryDto;

class CountryByBinResolver implements CountryResolverInterface
{
    private array $binMap = [];

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ConfigProviderInterface $config,
    ) {
    }

    public function resolve(string $bin): CountryDto
    {
        // qty of bins are limited, maybe it's worth to same them into permanent cache.
        if (isset($this->binMap[$bin])) {
            return $this->binMap[$bin];
        }

        $response = $this->client->request(
            'GET',
            sprintf('%s/%s', $this->config->getCountryResolverUrl(), urlencode($bin)),
            [
                'headers' => [
                    'Accept-Version' => '3',
                ],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new CantGetCountryException('Can\'t get data from the server.');
        }

        $content = $response->toArray();

        if (!isset($content['country']['alpha2'])) {
            throw new CantGetCountryException('Response is in the unexpected format.');
        }

        $this->binMap[$bin] = new CountryDto(
            trim($content['country']['alpha2']),
        );

        return $this->binMap[$bin];
    }
}
