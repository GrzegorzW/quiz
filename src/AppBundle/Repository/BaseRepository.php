<?php

namespace AppBundle\Repository;

use ModernFactory\ResourcesBundle\Doctrine\ORM\EntityRepository;
use ModernFactory\ResourcesBundle\Resource\Model\ResourceInterface;

/**
 * Class BaseRepository
 * @package AppBundle\Repository
 */
abstract class BaseRepository extends EntityRepository
{
    /**
     * @param ResourceInterface $resource
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(ResourceInterface $resource)
    {
        $this->getEntityManager()->persist($resource);
        $this->getEntityManager()->flush();
    }

    /**
     * @param ResourceInterface $resource
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(ResourceInterface $resource)
    {
        $this->getEntityManager()->remove($resource);
        $this->getEntityManager()->flush();
    }
}
