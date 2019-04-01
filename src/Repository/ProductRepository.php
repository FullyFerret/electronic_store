<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * List all products.
     *
     * @return array
     */
    public function listAll() {
        return $this->createQueryBuilder('p')
            ->select("p.id,
                      p.name, 
                      c.name AS category,
                      p.sku, 
                      p.price, 
                      p.quantity,
                      DATE_FORMAT(p.created_at, '%Y-%m-%dT%T+0000') AS created_at,
                      DATE_FORMAT(p.modified_at, '%Y-%m-%dT%T+0000') AS modified_at")
            ->leftJoin("p.category", "c")
            ->orderBy("p.created_at", "DESC")
            ->getQuery()
            ->getScalarResult();
    }
}
