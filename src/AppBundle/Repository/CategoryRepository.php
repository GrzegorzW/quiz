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
}
