<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public const BULK_INSERT_COUNT = 100;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function getCurrencyByCode(string $codeFrom, string $codeTo): ?Currency
    {
        return $this->findOneBy(['codeFrom' => $codeFrom, 'codeTo' => $codeTo]);
    }

    public function getAllCurrencyByCode(string $currencyCode): array
    {
        return $this->findBy(['codeFrom' => $currencyCode]);
    }


    public function getAvailableCurrencies(): array
    {
        return $this->getEntityManager()->getConnection()->fetchFirstColumn(
            <<<SQL
                select distinct code_from from currency
            SQL,
        );
    }

    public function bulkInsertOrUpdate(array $currencies): void
    {
        $this->getEntityManager()->wrapInTransaction(
            function($entityManager) use ($currencies) {
                $i = 0;
                foreach ($currencies as $currency) {
                    $entityManager->persist($currency);
                    $i++;
                    if (($i % self::BULK_INSERT_COUNT) === 0) {
                        $entityManager->flush();
                        $entityManager->clear();
                    }
                }
                $entityManager->flush();
                $entityManager->clear();
            }
        );
    }

    public function bulkDelete(array $currencies): void
    {
        $this->getEntityManager()->wrapInTransaction(
            function($entityManager) use ($currencies) {
                foreach ($currencies as $currency) {
                    $entityManager->remove($currency);
                }
                $entityManager->flush();
                $entityManager->clear();
            }
        );
    }
}
