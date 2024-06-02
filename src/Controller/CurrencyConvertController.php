<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CurrencyConvertRequestDTO;
use App\Service\CurrencyService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/currency-convert', name: 'currency_convert', methods: ['GET'])]
readonly class CurrencyConvertController
{
    public function __construct(
        private CurrencyService $currencyService
    ) {
    }

    public function __invoke(
        #[MapQueryString] CurrencyConvertRequestDTO $convertRequestDTO
    ): Response {
        try {
            $convertedAmount = $this->currencyService->convert(
                $convertRequestDTO->fromCurrency,
                $convertRequestDTO->toCurrency,
                $convertRequestDTO->amount
            );
        } catch (\Throwable $exception) {
            return new JsonResponse(
                [
                    'error' => $exception->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            [
                'converted_amount' => $convertedAmount,
            ]
        );
    }
}
