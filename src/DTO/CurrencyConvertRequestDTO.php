<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CurrencyConvertRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 3)]
        #[Assert\Currency]
        public string $fromCurrency,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 3)]
        #[Assert\Currency]
        public string $toCurrency,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public float $amount = 0.0,
    ) {
    }

}
