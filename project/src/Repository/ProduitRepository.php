<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function getPaginatedProducts($limit, $offset, $search = null, $maxPrice = null)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if ($search) {
            $queryBuilder->andWhere('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($maxPrice) {
            $queryBuilder->andWhere('p.price <= :max_price')
                ->setParameter('max_price', $maxPrice);
        }

        $queryBuilder->setMaxResults($limit)
            ->setFirstResult($offset);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countProducts($search = null, $maxPrice = null)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');

        if ($search) {
            $queryBuilder->andWhere('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($maxPrice) {
            $queryBuilder->andWhere('p.price <= :max_price')
                ->setParameter('max_price', $maxPrice);
        }

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function findOneWithReviews($id)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.reviews', 'r')
            ->addSelect('r')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
