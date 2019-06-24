<?php

namespace App\Repository;

use App\Entity\CountrySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query;

/**
 * @method CountrySearch|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountrySearch|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountrySearch[]    findAll()
 * @method CountrySearch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountrySearchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CountrySearch::class);
    }

    /**
     * Return the last search inserted
     *
     * @param int $limit
     *
     * @return mixed
     */
    public function getLastSearchs($limit)
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

    }
}
