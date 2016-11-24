<?php

namespace AppBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class PaginationManager
{
    public function paginate(QueryBuilder $queryBuilder, array $sorting = [])
    {
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $sorting
     *
     * @author Paweł Jędrzejewski <pawel@sylius.org>
     */
    public function applySorting(QueryBuilder $queryBuilder, array $sorting = [])
    {
        foreach ($sorting as $property => $order) {
            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property), $order);
            }
        }
    }

    /**
     * @param string $name
     *
     * @return string
     * @author Paweł Jędrzejewski <pawel@sylius.org>
     */
    protected function getPropertyName($name)
    {
        if (false === strpos($name, '.')) {
            return 'o.' . $name;
        }

        return $name;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Pagerfanta
     * @author Paweł Jędrzejewski <pawel@sylius.org>
     */
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        return new Pagerfanta(new DoctrineORMAdapter($queryBuilder, true, false));
    }
}
