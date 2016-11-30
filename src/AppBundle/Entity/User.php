<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("emailCanonical")
 */
class User extends BaseUser implements QuizResourceInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /** @var string */
    protected $shortId;
    /** @var \DateTime */
    protected $createdAt;
    /** @var \DateTime */
    protected $updatedAt;
    /** @var bool */
    protected $deleted;

    public function __construct()
    {
        parent::__construct();
        $this->enabled = true;
        $this->deleted = false;
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

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @param string $email
     * @return $this|\FOS\UserBundle\Model\UserInterface
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }
}
