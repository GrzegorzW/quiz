<?php

namespace AppBundle\Service;

use AppBundle\Entity\Answer;
use AppBundle\Entity\AnswersResult;
use AppBundle\Repository\AnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnswersManager
{
    private $answerRepository;

    public function __construct(AnswerRepository $answerRepository)
    {
        $this->answerRepository = $answerRepository;
    }

    /**
     * @param array $answersInput
     * @return ArrayCollection
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function handleAnswers(array $answersInput)
    {
        if (!is_array($answersInput)) {
            throw new BadRequestHttpException('User answers must be type of array.');
        }

        $answers = new ArrayCollection();

        foreach ($answersInput as $shortId) {
            $this->throwIfInvalidShortIdType($shortId);

            $answer = $this->answerRepository->findAnswerByShortId($shortId);
            if (!$answer instanceof Answer) {
                throw new NotFoundHttpException(sprintf('Answer with id %s not found.', $shortId));
            }

            if (!$answers->contains($answer)) {
                $answers->add($answer);
            }
        }

        return $answers;
    }

    /**
     * @param $shortId
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    private function throwIfInvalidShortIdType($shortId)
    {
        if (!is_string($shortId)) {
            throw new BadRequestHttpException('Id must be string.');
        }
    }

    /**
     * @param ArrayCollection $answers
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getCorrectAnswersCount(ArrayCollection $answers)
    {
        $count = 0;

        /** @var Answer $answer */
        foreach ($answers as $answer) {
            if (!$answer instanceof Answer) {
                throw new \InvalidArgumentException();
            }

            if ($answer->getQuestion()->getCorrectAnswer()->getId() === $answer->getId()) {
                $count++;
            }
        }

        return $count;
    }
}
