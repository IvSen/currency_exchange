<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ExchangeRateApiService
{
    public function __construct(
        private string $exchangerateApiKey,
        private HttpClientInterface $exchangerateClient,
    ) {
    }

    public function getExchangeRate(string $currencyCode): array
    {
        $response = $this->exchangerateClient->request(
            'GET',
            sprintf(
                '/v6/%s/latest/%s',
                $this->exchangerateApiKey,
                $currencyCode
            )
        );

        $data = $response->toArray();

        if ($data['result'] !== 'success') {
            throw new Exception('Error getting exchange rate');
        }

        return $data;
    }

    public function getCurrenciesCode(array $conversionRates): array
    {
        return array_keys($conversionRates);
    }
}
