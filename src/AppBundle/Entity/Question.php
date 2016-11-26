<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Question implements QuizResourceInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    /** @var integer */
    protected $id;
    /** @var string */
    protected $shortId;
    /** @var \DateTime */
    protected $createdAt;
    /** @var \DateTime */
    protected $updatedAt;
    /** @var  string */
    protected $status;
    /** @var string */
    protected $content;
    /** @var Category */
    protected $category;
    /** @var Image */
    protected $image;
    /** @var ArrayCollection */
    protected $incorrectAnswers;
    /** @var Answer */
    protected $correctAnswer;

    public function __construct(Category $category)
    {
        $this->category = $category;
        $this->incorrectAnswers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Image $image
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
        $this->image->setQuestion($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getIncorrectAnswers()
    {
        return $this->incorrectAnswers;
    }

    /**
     * @param ArrayCollection $incorrectAnswers
     */
    public function setIncorrectAnswers(ArrayCollection $incorrectAnswers)
    {
        $this->incorrectAnswers = $incorrectAnswers;
    }

    /**
     * @return Answer
     */
    public function getCorrectAnswer()
    {
        return $this->correctAnswer;
    }

    /**
     * @param Answer $correctAnswer
     */
    public function setCorrectAnswer(Answer $correctAnswer)
    {
        $this->correctAnswer = $correctAnswer;
        $correctAnswer->setQuestion($this);
    }

    public function addIncorrectAnswer(Answer $answer)
    {
        if (!$this->incorrectAnswers->contains($answer)) {
            $this->incorrectAnswers->add($answer);
            $answer->setQuestion($this);
        }
    }

    public function removeIncorrectAnswer(Answer $answer)
    {
        if ($this->incorrectAnswers->contains($answer)) {
            $this->incorrectAnswers->removeElement($answer);
        }
    }
}
