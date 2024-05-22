<?php

declare(strict_types=1);

namespace CommissionCalculator\Unit\Service\CountryResolver;


use PHPUnit\Framework\TestCase;
use CommissionCalculator\Exception\NegativeRateException;
use PHPUnit\Framework\Attributes\ExpectedException;
use PHPUnit\Framework\Attributes\Test;
use CommissionCalculator\Service\CountryResolver\CountryByBinResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use CommissionCalculator\Api\ConfigProviderInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use CommissionCalculator\Exception\CantGetCountryException;

class CountryByBinResolverTest extends TestCase
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
        $sut = new CountryByBinResolver(
            client: $this->httpClient,
            config: $this->config,
        );

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('toArray')->willReturn(['country' => ['alpha2' => 'PL']]);

        $actualResult = $sut->resolve('bin');
        $this->assertEquals('PL', $actualResult->alpha2);
        // check if the second call we would use cache.
        $actualResult = $sut->resolve('bin');
        $this->assertEquals('PL', $actualResult->alpha2);
    }

    #[Test]
    public function testResolveCountryByBin400Exception()
    {
        $sut = new CountryByBinResolver(
            client: $this->httpClient,
            config: $this->config,
        );

        $this->response->method('getStatusCode')->willReturn(400);
        $this->expectException(CantGetCountryException::class);
        $sut->resolve('bin');
    }

    #[Test]
    public function testResolveCountryByBinWrongJsonException()
    {
        $sut = new CountryByBinResolver(
            client: $this->httpClient,
            config: $this->config,
        );

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('toArray')->willReturn(['country' => ['alpha3' => 'PL']]);

        $this->expectException(CantGetCountryException::class);
        $sut->resolve('bin');
    }
}