<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class AnswersResult
{
    /** @var int */
    protected $score;
    /** @var array */
    protected $answers = [];

    public function __construct($score, ArrayCollection $answers)
    {
        $this->score = $score;
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            if ($answer instanceof Answer) {
                $this->addAnswerItem($answer);
            }
        }
    }

    private function addAnswerItem(Answer $answer)
    {
        $this->answers[] = [
            'given' => $answer,
            'correct' => $answer->getQuestion()->getCorrectAnswer()
        ];
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
