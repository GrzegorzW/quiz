<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use ModernFactory\ResourcesBundle\Resource\Model\ResourceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("emailCanonical")
 */
class User extends BaseUser implements ResourceInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /** @var string */
    protected $shortId;
    /** @var \DateTime */
    protected $createdAt;
    /** @var \DateTime */
    protected $updatedAt;

    public function __construct()
    {
        parent::__construct();
        $this->enabled = true;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getShortId()
    {
        return $this->shortId;
    }

    /**
     * @param string $shortId
     */
    public function setShortId($shortId)
    {
        $this->shortId = $shortId;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
