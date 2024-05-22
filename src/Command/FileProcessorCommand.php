<?php

declare(strict_types=1);

namespace CommissionCalculator\Command;

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

class FileProcessorCommand extends Command implements InputProcessorInterface
{
    public function __construct(
        private readonly CountryResolverInterface $countryResolver,
        private readonly ExchangeRateResolverInterface $exchangeRateResolver,
        private readonly ComissionCalculatorInterface $commissionCalculator,
        private readonly TransactionsFileReaderInterface $fileReader,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('process:file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to file with transactions.')
            ->setDescription('Process file with transactions and calculate commissions to stdout.')
            ->setHelp('./comission-calculator process:file <path-to-the-file>');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->testAbleExecute($input, $output);
    }

    public function testAbleExecute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $line = 1;
        try {
            foreach ($this->fileReader->iterate($filePath) as $data) {
                try {
                    $transactionDto = new TransactionDto(
                        bin: (string)$data['bin'],
                        amount: (float)$data['amount'],
                        currency: (string)$data['currency'],
                    );
                    $countryDto = $this->countryResolver->resolve($transactionDto->bin);
                    $exchangeRate = $this->exchangeRateResolver->getRateToEUR($transactionDto->currency);
                    $calculationDto = new CommissionCalculationDto(
                        transaction: $transactionDto,
                        currencyRate: $exchangeRate,
                        country: $countryDto,
                    );
                    $output->writeln((string) $this->commissionCalculator->calculate($calculationDto));
                } catch (RateServerUnreachableException) {
                    $output->writeln('Rate server is unreachable, there is no way to calculate commission.');
                    return Command::FAILURE;
                } catch (\Throwable $e) {
                    $this->logger->error(sprintf(
                        'Error while processing transaction: file: %s; line: %s; error: %s',
                        $filePath,
                        $line,
                        $e->getMessage(),
                    ));
                } finally {
                    $line++;
                }
            }
        } catch (\Throwable $e) {
            $output->writeln('Can\'t read file. Check the file path and permissions.');
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
