<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CurrencyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/get-available-currencies', name: 'get_available_currencies', methods: ['GET'])]
readonly class GetAvailableCurrenciesController
{
    public function __construct(
        private CurrencyService $currencyService
    ) {
    }

    public function __invoke(): Response
    {
        $availableCurrencies = $this->currencyService->getAvailableCurrencies();

        return new JsonResponse([
            'currencies' => $availableCurrencies,
        ]);
    }
}
