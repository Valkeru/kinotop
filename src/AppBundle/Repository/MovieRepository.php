<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;

/**
 * MovieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MovieRepository extends \Doctrine\ORM\EntityRepository
{
    private $qb;

    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->qb = $this->createQueryBuilder('m');
    }

    public function getAllByDate(\DateTime $dateTime): array
    {
        return $this->qb->andWhere('m.inTopDate = :date')
            ->setParameter('date', $dateTime->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function getAllForCurrentDay(): array
    {
        return $this->qb->andWhere('m.inTopDate = :date')
            ->setParameter('date', (new \DateTime())->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function getInTopDates(): array
    {
        $result = $this->qb->select('m.inTopDate')
            ->distinct(true)
            ->orderBy('m.inTopDate', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $dates = [];

        foreach ($result as $item) {
            $dates[] = $item['inTopDate'];
        }

        return $dates;
    }

    public function resetQueryBuilder(): self
    {
        $alias    = $this->qb->getRootAlias();
        $this->qb = $this->createQueryBuilder($alias);

        return $this;
    }
}