<?php

namespace AppBundle\Repository;


class UserRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function createUserPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        if (isset($criteria['query'])) {
            $queryBuilder
                ->orWhere($queryBuilder->expr()->like($this->getPropertyName('username'), ':query'))
                ->setParameter('query', '%' . addcslashes($criteria['query'], '%_') . '%');
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = [];
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }
}
