<?php

declare(strict_types=1);

namespace Command;


use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use CommissionCalculator\Api\InputProcessorInterface;
use CommissionCalculator\Dto\TransactionDto;
use CommissionCalculator\Dto\CommissionCalculationDto;
use CommissionCalculator\Api\CountryResolverInterface;
use CommissionCalculator\Api\ExchangeRateResolverInterface;
use CommissionCalculator\Api\ComissionCalculatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Psr\Log\LoggerInterface;
use CommissionCalculator\Exception\RateServerUnreachableException;
use CommissionCalculator\Api\TransactionsFileReaderInterface;
use CommissionCalculator\Command\FileProcessorCommand;
use CommissionCalculator\Dto\CountryDto;
use CommissionCalculator\Dto\CurrencyRateDto;

class FileProcessorCommandTest extends TestCase
{
    private  InputInterface $input;
    private  OutputInterface $output;
    private  TransactionsFileReaderInterface $fileReader;

    private LoggerInterface $logger;

    private CountryResolverInterface $countryResolver;

    private ExchangeRateResolverInterface $exchangeRateResolver;

    private ComissionCalculatorInterface $commissionCalculator;

    private FileProcessorCommand $sut;

    protected function setUp(): void
    {
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->fileReader = $this->createMock(TransactionsFileReaderInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->countryResolver = $this->createMock(CountryResolverInterface::class);
        $this->exchangeRateResolver = $this->createMock(ExchangeRateResolverInterface::class);
        $this->commissionCalculator = $this->createMock(ComissionCalculatorInterface::class);
        $this->sut = new FileProcessorCommand(
            $this->countryResolver,
            $this->exchangeRateResolver,
            $this->commissionCalculator,
            $this->fileReader,
            $this->logger
        );
    }

    #[Test]
    public function executeCalculation(): void
    {
        $data = [
            ['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR'],
            ['bin' => '516793', 'amount' => '50.00', 'currency' => 'USD'],
            ['bin' => '45417360', 'amount' => '10000.00', 'currency' => 'JPY'],
        ];

        $this->input->method('getArgument')
            ->willReturn('test.txt');
        $this->output
            ->method('writeln');

        $this->fileReader->expects($this->once())
            ->method('iterate')
            ->willReturn((new \ArrayObject($data))->getIterator()
            );
        $this->countryResolver
            ->expects($this->exactly(count($data)))
            ->method('resolve')
            ->willReturn(new CountryDto('LT', 'Lithuania'));
        $this->exchangeRateResolver
            ->expects($this->exactly(count($data)))
            ->method('getRateToEUR')
            ->willReturn(new CurrencyRateDto('USD', 1.1497));
        $this->commissionCalculator
            ->expects($this->exactly(count($data)))
            ->method('calculate')
            ->willReturn(0.5);
        $this->assertEquals(0, $this->sut->testAbleExecute($this->input, $this->output));
    }

    #[Test]
    public function executeCalculationRatesUnavailable(): void
    {
        $data = [
            ['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR'],
            ['bin' => '516793', 'amount' => '50.00', 'currency' => 'USD'],
            ['bin' => '45417360', 'amount' => '10000.00', 'currency' => 'JPY'],
        ];

        $this->input->method('getArgument')
            ->willReturn('test.txt');
        $this->output
            ->method('writeln');

        $this->fileReader->expects($this->once())
            ->method('iterate')
            ->willReturn((new \ArrayObject($data))->getIterator()
            );
        $this->countryResolver
            ->method('resolve')
            ->willReturn(new CountryDto('LT', 'Lithuania'));
        $this->exchangeRateResolver
            ->expects($this->once())
            ->method('getRateToEUR')
            ->willThrowException(new RateServerUnreachableException());
        $this->commissionCalculator
            ->method('calculate')
            ->willReturn(0.5);

        $this->assertEquals(1, $this->sut->testAbleExecute($this->input, $this->output));
    }
}