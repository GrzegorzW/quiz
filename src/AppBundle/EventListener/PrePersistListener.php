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

        $shortId = base64_encode(random_bytes(9));
        $entity->setShortId($shortId);
    }
}