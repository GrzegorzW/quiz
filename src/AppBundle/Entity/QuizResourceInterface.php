<?php

namespace AppBundle\Entity;

use ModernFactory\ResourcesBundle\Resource\Model\ResourceInterface;

interface QuizResourceInterface extends ResourceInterface
{
    /**
     * @param string
     */
    public function setShortId($shortId);

    /**
     * @return string
     */
    public function getShortId();
}
