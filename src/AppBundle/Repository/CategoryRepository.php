<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;

class CategoryRepository extends BaseRepository
{
    public function getCategoriesQB(array $statuses = [Category::STATUS_ENABLED])
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('statuses', $statuses);

        return $qb;
    }

    public function findCategoryByShortId(
        $shortId,
        array $statuses = [Category::STATUS_ENABLED]
    ) {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.shortId = :shortId')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('shortId', $shortId)
            ->setParameter('statuses', $statuses);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
