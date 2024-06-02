<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CurrencyRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 3)]
    private string $codeFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $codeTo;

    #[ORM\Column(type: 'float')]
    private float $rate;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTimeInterface $updatedAt;

    public function __construct(string $codeFrom, string $codeTo, float $rate)
    {
        $this->codeFrom = $codeFrom;
        $this->codeTo = $codeTo;
        $this->rate = $rate;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCodeFrom(): string
    {
        return $this->codeFrom;
    }

    public function getCodeTo(): string
    {
        return $this->codeTo;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }
}
