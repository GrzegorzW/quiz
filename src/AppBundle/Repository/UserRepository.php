<?php

namespace AppBundle\Repository;

class UserRepository extends BaseRepository
{
    public function findUserByShortId($shortId, $allowDisabled = false)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.deleted = :deleted')
            ->andWhere('o.shortId = :shortId')
            ->setParameter('deleted', false)
            ->setParameter('shortId', $shortId);

        if (!$allowDisabled) {
            $qb
                ->andWhere('o.enabled = :enabled')
                ->setParameter('enabled', true);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getUsersQB($phrase = null, $allowDisabled = false)
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.deleted = :deleted')
            ->setParameter('deleted', false);

        if (!$allowDisabled) {
            $qb
                ->andWhere('o.enabled = :enabled')
                ->setParameter('enabled', true);
        }

        if ($phrase) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('o.shortId', ':phrase'),
                    $qb->expr()->like('o.email', ':phrase')
                )
            );
            $qb->setParameter('phrase', '%' . trim($phrase) . '%');
        }

        return $qb;
    }
}
