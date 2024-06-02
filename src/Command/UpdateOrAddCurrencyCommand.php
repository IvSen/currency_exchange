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

#[AsCommand(name: 'app:update_or_add_currency')]
class UpdateOrAddCurrencyCommand extends Command
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly ExchangeRateApiService $exchangeRateApiService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $availableCurrencies = $this->currencyRepository->getAvailableCurrencies();

        $updatedCurrencies = [];
        $forDeleteCurrencies = [];
        foreach ($availableCurrencies as $availableCurrency) {
            $allCurrencyByCode = $this->currencyRepository->getAllCurrencyByCode($availableCurrency);
            $exchangeRateResponse = $this->exchangeRateApiService->getExchangeRate($availableCurrency);
            $exchangeRate = $exchangeRateResponse['conversion_rates'];

            foreach ($allCurrencyByCode as $currency) {
                if (!isset($exchangeRate[$currency->getCodeTo()])) {
                    $forDeleteCurrencies[] = $currency;
                    continue;
                }

                if ($exchangeRate[$currency->getCodeTo()] === $currency->getRate()) {
                    continue;
                }

                $currency->setRate($exchangeRate[$currency->getCodeTo()]);
                $updatedCurrencies[] = $currency;
                unset($exchangeRate[$availableCurrency]);
            }

            if (empty($exchangeRate) !== true) {
                foreach ($exchangeRate as $codeTo => $rate) {
                    $updatedCurrencies[] = new Currency($availableCurrency, $codeTo, $rate);
                }
            }
        }

        $this->currencyRepository->bulkInsertOrUpdate($updatedCurrencies);
        if ($forDeleteCurrencies !== []) {
            $this->currencyRepository->bulkDelete($forDeleteCurrencies);
        }

        return Command::SUCCESS;
    }
}
