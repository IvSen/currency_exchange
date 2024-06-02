<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\Service\ExchangeRateApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:initial_currency_data')]
class InitialCurrencyDataCommand extends Command
{
    private const DEFAULT_CURRENCY = 'USD';

    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly ExchangeRateApiService $exchangeRateApiService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exchangeRate = $this->exchangeRateApiService->getExchangeRate(self::DEFAULT_CURRENCY);
        $currenciesCode = $this->exchangeRateApiService->getCurrenciesCode($exchangeRate['conversion_rates']);

        $newCurrencies = $this->processingConversionRates(self::DEFAULT_CURRENCY, $exchangeRate['conversion_rates']);
        $this->currencyRepository->bulkInsertOrUpdate($newCurrencies);

        foreach ($currenciesCode as $currency) {
            if ($currency === self::DEFAULT_CURRENCY) {
                continue;
            }

            $exchangeRate = $this->exchangeRateApiService->getExchangeRate($currency);
            $newCurrencies = $this->processingConversionRates($currency, $exchangeRate['conversion_rates']);
            $this->currencyRepository->bulkInsertOrUpdate($newCurrencies);
        }

        return Command::SUCCESS;
    }

    private function processingConversionRates(string $codeFrom, array $conversionRates): array
    {
        $newCurrencies = [];
        foreach ($conversionRates as $codeTo => $rate) {
            $newCurrencies[] = new Currency($codeFrom, $codeTo, $rate);
        }

        return $newCurrencies;
    }
}
