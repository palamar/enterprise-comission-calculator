<?php

declare(strict_types=1);

namespace CommissionCalculator\Unit\Service\CountryResolver;

use CommissionCalculator\Api\ConfigProviderInterface;
use CommissionCalculator\Exception\CantGetRateException;
use CommissionCalculator\Exception\RateServerUnreachableException;
use CommissionCalculator\Service\ExchangeRateProvider\ExchangeRatesApiProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRateProviderTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private ConfigProviderInterface $config;

    private ResponseInterface $response;

    protected function setUp(): void
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);
        $this->config = $this->createMock(ConfigProviderInterface::class);
    }

    #[Test]
    public function testResolveCountryByBin()
    {
        $sut = new ExchangeRatesApiProvider(
            client: $this->httpClient,
            config: $this->config,
        );

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('toArray')->willReturn(['rates' => ['UAH' => '43.111714']]);

        $actualResult = $sut->getRateToEUR('UAH');
        $this->assertEquals(43.111714, $actualResult->rate);
        // check if the second call we would use cache.
        $actualResult = $sut->getRateToEUR('uAH');
        $this->assertEquals(43.111714, $actualResult->rate);
    }

    #[Test]
    public function testResolveCountryByBin400Exception()
    {
        $sut = new ExchangeRatesApiProvider(
            client: $this->httpClient,
            config: $this->config,
        );

        $this->response->method('getStatusCode')->willReturn(400);
        $this->expectException(RateServerUnreachableException::class);
        $sut->getRateToEUR('uah');
    }

    #[Test]
    public function testResolveCountryByBinWrongJsonException()
    {
        $sut = new ExchangeRatesApiProvider(
            client: $this->httpClient,
            config: $this->config,
        );

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('toArray')->willReturn([]);

        $this->expectException(CantGetRateException::class);
        $sut->getRateToEUR('uah');
    }

    #[Test]
    public function testResolveCountryByBinWrongMissedCurrencyException()
    {
        $sut = new ExchangeRatesApiProvider(
            client: $this->httpClient,
            config: $this->config,
        );

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('toArray')->willReturn(['rates' => ['UAH' => '43.111714']]);

        $this->expectException(CantGetRateException::class);
        $sut->getRateToEUR('DNK');
        // check if the second call we would use cache.
        $this->expectException(CantGetRateException::class);
        $sut->getRateToEUR('DNK');
    }
}
