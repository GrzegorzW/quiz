<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;

class CategoryRepository extends BaseRepository
{
    public function getCategoriesQB($phrase)
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.status = :enabled')
            ->setParameter('enabled', Category::STATUS_ENABLED);

        if ($phrase) {
            $qb->andWhere('o.name LIKE :phrase')
                ->setParameter('phrase', '%' . trim($phrase) . '%');
        }

        return $qb;
    }
}
