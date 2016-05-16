<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ProductRepository extends EntityRepository
{
    public function findLatest($limit = 5)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT p
                FROM AppBundle:Product p
                ORDER BY p.created DESC
            ')
            ->setMaxResults( $limit );

        return $query->getResult();
    }

    public function findByTag($tag = '')
    {
        return $this->getEntityManager()->getRepository('AppBundle:Product')
            ->createQueryBuilder('p')
            ->leftJoin('p.tags','t')
            ->orWhere('t.name like :tag')
            ->setParameter(':tag', '%'.$tag.'%')
            ->orderBy('p.created', 'DESC')
            ->getQuery();
    }
}
