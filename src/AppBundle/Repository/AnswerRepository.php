<?php

namespace AppBundle\Repository;

class AnswerRepository extends BaseRepository
{
    public function findAnswerByShortId($shortId)
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.shortId = :shortId')
            ->setParameter('shortId', $shortId);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
