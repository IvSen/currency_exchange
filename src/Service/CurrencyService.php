<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\CurrencyPairNotFoundException;
use App\Repository\CurrencyRepository;

final class CurrencyService
{
    public const SCALE = 4;

    public function __construct(
        private readonly CurrencyRepository $currencyRepository
    ) {
    }

    public function convert(string $fromCurrency, string $toCurrency, float $amount): string
    {
        $toCurrencyRate = $this->currencyRepository->getCurrencyByCode($fromCurrency, $toCurrency);

        if (!$toCurrencyRate) {
            throw new CurrencyPairNotFoundException();
        }

        return $this->calculateAmount($amount, $toCurrencyRate->getRate());
    }

    private function calculateAmount(float $amount, float $rate): string
    {
        return bcmul((string)$amount, (string)$rate, self::SCALE);
    }

    public function getAvailableCurrencies(): array
    {
        return $this->currencyRepository->getAvailableCurrencies();
    }

}
