<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\QuizResourceInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PrePersistListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof QuizResourceInterface) {
            return;
        }

        $hash = hash('sha512', random_bytes(random_int(16, 128)) . random_int(PHP_INT_MIN, PHP_INT_MAX));

        $shortId = substr($hash, 0, 10);
        $entity->setShortId($shortId);
    }
}
