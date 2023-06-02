<?php

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Url|null find($id, $lockMode = null, $lockVersion = null)
 * @method Url|null findOneBy(array $criteria, array $orderBy = null)
 * @method Url[]    findAll()
 * @method Url[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findFirstByHash(string $value): ?Url
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.hash = :val')
            ->setParameter('val', $value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * return urls
     *
     * @param  string|null  $date_start
     * @param  string|null  $date_end
     * @param  string|null  $domain
     *
     * @return float|int|string|array
     */
    public function findAllByUrl(string $dateStart = null, string $dateEnd = null, string $domainName = null): float|int|array|string
    {
         $query = $this->createQueryBuilder('u');

         if($dateStart && $dateEnd) {
             $query->where('u.createdDate BETWEEN :date_start AND :date_end')
                     ->setParameter('date_start', $dateStart)
                     ->setParameter('date_end', $dateEnd);
         } elseif ($dateStart) {
             $query->where('u.createdDate >= :date_start')
                 ->setParameter('date_start',  $dateStart);
         }  elseif ($dateEnd){
             $query->where('u.createdDate <= :date_end')
                 ->setParameter('date_end', $dateEnd);
         }
         if($domainName) {
             $query->andWhere($query->expr()->like('u.url', ':domain'))
                 ->setParameter('domain', '%' . $domainName . '%');
         }

         return $query->getQuery()->getArrayResult();
    }
}
