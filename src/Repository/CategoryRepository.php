<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }


    /**
     * List all categories.
     *
     * @return array
     */
    public function listAll() {
        return $this->createQueryBuilder('c')
            ->select("c.id,
                      c.name AS name,
                      DATE_FORMAT(c.created_at, '%Y-%m-%dT%T+0000') AS created_at,
                      DATE_FORMAT(c.modified_at, '%Y-%m-%dT%T+0000') AS modified_at")
            ->orderBy("c.created_at", "DESC")
            ->getQuery()
            ->getScalarResult();
    }
}
