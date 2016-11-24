<?php

namespace AppBundle\Entity;

use ModernFactory\ResourcesBundle\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Image implements ResourceInterface
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $shortId;
    /** @var string */
    protected $filesystem;
    /** @var string */
    protected $originalName;
    /** @var string */
    protected $imageName;
    /** @var string */
    protected $mimeType;
    /** @var int */
    protected $size;
    /** @var Question */
    protected $question;
    /** @var \DateTime */
    protected $updatedAt;
    /** @var \DateTime */
    protected $createdAt;
    /** @var string */
    protected $url;
    /** @var UploadedFile */
    protected $imageFile;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return string
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param string $filesystem
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get image file.
     *
     * @return UploadedFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $imageFile
     */
    public function setImageFile(UploadedFile $imageFile = null)
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getImagePath()
    {
        return null === $this->imageName
            ? null
            : $this->getUploadDir() . '/' . $this->imageName;
    }

    public function getUploadDir()
    {
        return 'images';
    }

    public function getAbsoluteImagePath()
    {
        return null === $this->imageName
            ? null
            : $this->getUploadDir() . '/' . $this->imageName;
    }

    /**
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param Question $question
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;
    }
}
